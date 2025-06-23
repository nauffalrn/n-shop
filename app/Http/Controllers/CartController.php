<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'UserOnly']);
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
        
        $totalPrice = 0;
        foreach($carts as $cart) {
            $totalPrice += $cart->getTotalPrice();
        }

        return view('user.show_cart', compact('carts', 'totalPrice'));
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
}