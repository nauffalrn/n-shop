<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\Promo;
use Illuminate\Support\Facades\Log;
use App\Models\Address;

class CartController extends Controller
{
    public function __construct()
    {
        // Ubah middleware untuk tidak berlaku ke semua method
        $this->middleware(['auth', 'UserOnly'])->except(['applyPromo', 'removePromo']);
        
        // Tambahkan middleware auth khusus untuk promo
        $this->middleware('auth')->only(['applyPromo', 'removePromo']);
    }

    public function add_to_cart(Product $product, Request $request)
    {
        $user_id = Auth::id();
        $product_id = $product->id;
        $variant_id = $request->product_variant_id;

        // Validasi variant jika ada
        if($variant_id) {
            $variant = ProductVariant::find($variant_id);
            if(!$variant || $variant->product_id != $product_id) {
                return Redirect::back()->with('error', 'Variant produk tidak valid!');
            }
            $availableStock = $variant->stock;
        } else {
            $availableStock = $product->stock;
        }

        $existing_cart = Cart::where('user_id', $user_id)
            ->where('product_id', $product_id)
            ->where('product_variant_id', $variant_id)
            ->first(); 

        if ($existing_cart == null) {
            $request->validate([
                'amount' => 'required|gte:1|lte:' . $availableStock,
            ]);

            Cart::create([
                'user_id' => $user_id,
                'product_id' => $product_id,
                'product_variant_id' => $variant_id,
                'amount' => $request->amount
            ]);
        } else {
            $maxAmount = $availableStock - $existing_cart->amount;
            $request->validate([
                'amount' => 'required|gte:1|lte:' . $maxAmount,
            ]);

            $existing_cart->update([
                'amount' => $existing_cart->amount + $request->amount
            ]);
        }

        return Redirect::route('show_cart')->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function show_cart()
    {
        $user_id = Auth::id();
        $carts = Cart::with(['product', 'variant'])->where('user_id', $user_id)->get();
        
        if($carts->isEmpty()) {
            return view('user.show_cart', ['carts' => collect()]);
        }
        
        $totalPrice = 0;
        $totalWeight = 0;
        foreach($carts as $cart) {
            $totalPrice += $cart->getTotalPrice();
            
            // Pastikan weight selalu ada, dengan fallback ke 0.1 kg jika null
            $itemWeight = ($cart->product->weight ?? 0.1);
            $totalWeight += $itemWeight * $cart->amount;
        }
        
        // Ambil promo dari session jika ada
        $promoId = session('promo_id');
        $discount = session('promo_discount', 0);
        $promo = $promoId ? Promo::find($promoId) : null;
        
        // Cek apakah user punya alamat
        $hasAddresses = Address::where('user_id', $user_id)->exists();
        
        return view('user.show_cart', compact('carts', 'totalPrice', 'totalWeight', 'promo', 'discount', 'hasAddresses'));
    }

    public function update_cart(Request $request, Cart $cart)
    {
        // Cek kepemilikan cart
        if($cart->user_id != Auth::id()) {
            return Redirect::route('show_cart')->with('error', 'Akses ditolak!');
        }

        $availableStock = $cart->variant ? $cart->variant->stock : $cart->product->stock;
        
        $request->validate([
            'amount' => 'required|gte:1|lte:' . $availableStock,
        ]);

        $cart->update(['amount' => $request->amount]);

        return Redirect::route('show_cart')->with('success', 'Keranjang berhasil diupdate!');
    }

    public function delete_cart(Cart $cart)
    {
        // Cek kepemilikan cart
        if($cart->user_id != Auth::id()) {
            return Redirect::route('show_cart')->with('error', 'Akses ditolak!');
        }

        $cart->delete();
        return Redirect::back()->with('success', 'Item berhasil dihapus dari keranjang!');
    }

    // Tambahkan method untuk memproses promo
    public function applyPromo(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'promo_code' => 'required|string'
            ], [
                'promo_code.required' => 'Kode promo tidak boleh kosong'
            ]);

            $user_id = Auth::id();
            $carts = Cart::with(['product', 'variant'])->where('user_id', $user_id)->get();
            
            if ($carts->isEmpty()) {
                return redirect()->back()->with('error', 'Keranjang Anda kosong!');
            }
            
            $totalPrice = 0;
            foreach($carts as $cart) {
                $totalPrice += $cart->getTotalPrice();
            }
            
            // Log untuk debugging tanpa menampilkan error
            Log::info('Mencari promo dengan kode: ' . $request->promo_code);
            
            // Cari promo berdasarkan kode
            $promo = Promo::where('code', $request->promo_code)->first();
            
            if (!$promo) {
                return redirect()->back()->with('error', 'Kode promo tidak valid atau tidak ditemukan.');
            }
            
            Log::info('Promo ditemukan: ' . $promo->code);
            
            // Periksa validitas promo
            if (!$promo->isValid($totalPrice)) {
                $invalidReason = '';
                
                if (!$promo->is_active) {
                    $invalidReason = 'Kode promo tidak aktif.';
                } else if ($promo->starts_at && $promo->starts_at->gt(now())) {
                    $invalidReason = 'Kode promo belum berlaku.';
                } else if ($promo->expires_at && $promo->expires_at->lt(now())) {
                    $invalidReason = 'Kode promo sudah tidak berlaku.';
                } else if ($promo->max_uses > 0 && $promo->used_count >= $promo->max_uses) {
                    $invalidReason = 'Kode promo sudah mencapai batas penggunaan maksimal.';
                } else if ($promo->min_purchase > 0 && $totalPrice < $promo->min_purchase) {
                    $invalidReason = 'Total belanja belum memenuhi minimum pembelian untuk promo ini (min. Rp '.number_format($promo->min_purchase, 0, ',', '.').')';
                }
                
                return redirect()->back()->with('error', 'Kode promo tidak valid: ' . $invalidReason);
            }
            
            // Hitung diskon
            $discount = $promo->calculateDiscount($totalPrice);
            
            // Simpan promo di session
            session(['promo_id' => $promo->id, 'promo_discount' => $discount]);
            
            Log::info('Promo berhasil diterapkan: ' . $promo->code . ' dengan diskon: ' . $discount);
            
            return redirect()->back()->with('success', 'Kode promo berhasil diterapkan! Anda mendapatkan potongan sebesar Rp ' . number_format($discount, 0, ',', '.'));
        } catch (\Exception $e) {
            // Log error untuk debugging tanpa menampilkan detail teknis ke user
            Log::error('Error applying promo: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menerapkan kode promo. Silakan coba lagi.');
        }
    }

    public function removePromo()
    {
        // Hapus promo dari session
        session()->forget(['promo_id', 'promo_discount']);
        
        return redirect()->back()->with('success', 'Kode promo berhasil dihapus.');
    }
}