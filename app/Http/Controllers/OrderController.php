<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Promo;
use App\Models\Address;
use App\Services\ShippingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    protected $shippingService;
    
    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }
    
    public function selectShipping()
    {
        $user_id = Auth::id();
        $carts = Cart::with(['product', 'variant'])->where('user_id', $user_id)->get();
        
        if($carts->isEmpty()) {
            return redirect()->route('show_cart')->with('error', 'Keranjang Anda kosong!');
        }
        
        $addresses = Address::where('user_id', $user_id)->get();
        
        if($addresses->isEmpty()) {
            return redirect()->route('address.create')
                ->with('info', 'Silakan tambahkan alamat pengiriman terlebih dahulu.');
        }
        
        $totalPrice = 0;
        $totalWeight = 0;
        foreach($carts as $cart) {
            $totalPrice += $cart->getTotalPrice();
            $totalWeight += ($cart->product->weight ?? 0.1) * $cart->amount;
        }
        
        // Ambil promo dari session jika ada
        $promoId = session('promo_id');
        $discount = session('promo_discount', 0);
        $promo = $promoId ? Promo::find($promoId) : null;
        
        return view('user.select_shipping', compact('carts', 'addresses', 'totalPrice', 'totalWeight', 'promo', 'discount'));
    }
    
    public function calculateShipping(Request $request)
    {
        $address = Address::findOrFail($request->address_id);
        $totalWeight = $request->weight;
        
        $shippingCost = $this->shippingService->calculate(
            $address->province,
            $address->country,
            $totalWeight
        );
        
        return response()->json([
            'shipping_cost' => $shippingCost,
            'formatted_cost' => 'Rp ' . number_format($shippingCost, 0, ',', '.'),
        ]);
    }
    
    public function checkout(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
        ]);
        
        $user_id = Auth::id();
        $carts = Cart::where('user_id', $user_id)->get();
        
        if($carts->isEmpty()) {
            return redirect()->route('show_cart')->with('error', 'Keranjang Anda kosong!');
        }
        
        $address = Address::findOrFail($request->address_id);
        
        // Hitung total berat
        $totalWeight = 0;
        foreach($carts as $cart) {
            $totalWeight += ($cart->product->weight ?? 0.1) * $cart->amount;
        }
        
        // Hitung ongkir
        $shippingCost = $this->shippingService->calculate(
            $address->province,
            $address->country,
            $totalWeight
        );
        
        // Buat order baru
        $order = Order::create([
            'user_id' => $user_id,
            'is_paid' => false,
            'promo_id' => session('promo_id'),
            'discount' => session('promo_discount', 0),
            'shipping_cost' => $shippingCost,
            'address_id' => $address->id,
            'shipping_address' => json_encode([
                'name' => $address->name,
                'phone' => $address->phone,
                'address' => $address->address,
                'city' => $address->city,
                'district' => $address->district,
                'province' => $address->province,
                'postal_code' => $address->postal_code,
                'country' => $address->country,
            ])
        ]);
        
        // Lanjutkan dengan kode existing untuk transaksi produk
        foreach($carts as $cart) {
            $product = Product::find($cart->product_id);
            
            // Pastikan amount tidak null dan memiliki nilai default
            $amount = $cart->amount ?? 1; // Default ke 1 jika amount null
            
            $product->update([
                'stock' => $product->stock - $amount
            ]);
            
            Transaction::create([
                'amount' => $amount,
                'order_id' => $order->id,
                'product_id' => $cart->product_id
            ]);
            
            $cart->delete();
        }
        
        // Jika ada promo, tambahkan penggunaan
        if ($order->promo_id) {
            $promo = Promo::find($order->promo_id);
            $promo->increment('used_count');
            
            // Hapus promo dari session
            session()->forget(['promo_id', 'promo_discount']);
        }
        
        return redirect()->route('show_order', $order)->with('success', 'Checkout berhasil! Silahkan lakukan pembayaran.');
    }

    public function index_order(){
        $user = Auth::user();
        $is_admin = Auth::user()->is_admin;
        
        if($is_admin){
            $orders = Order::with(['user', 'transactions.product'])
                        ->orderBy('created_at', 'desc')
                        ->get();
        } else {
            $orders = Order::with(['user', 'transactions.product'])
                        ->where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')  
                        ->get();
        }
        
        return view('user.index_order', compact('orders')); 
    }

    public function show_order(Order $order){
        $user = Auth::user();
        $is_admin = Auth::user()->is_admin;

        $order->load(['user', 'transactions.product']);

        if ($is_admin || $order->user_id == $user->id){
            return view('user.show_order', compact('order'));
        }

        return Redirect::route('index_order');
    }

    public function submit_payment_receipt(Request $request, Order $order)
    {
        $file = $request->file('payment_receipt');
        $path = time() . '_' . $order->id . '.' . $file->getClientOriginalExtension();

        Storage::disk('local')->put('public/' . $path, file_get_contents($file));

        $order->update([
            'payment_receipt' => $path,
            'payment_status' => Order::PAYMENT_AWAITING_CONFIRMATION
        ]);

        return Redirect::back()->with('success', 'Bukti pembayaran berhasil diunggah dan menunggu konfirmasi admin.');
    }

    public function confirm_payment(Order $order)
    {
        $order->update([
            'is_paid' => true,
            'payment_status' => Order::PAYMENT_CONFIRMED
        ]);
        return Redirect::back()->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    public function rejectPayment(Request $request, Order $order)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ], [
            'rejection_reason.required' => 'Alasan penolakan harus diisi',
            'rejection_reason.max' => 'Alasan penolakan maksimal 255 karakter'
        ]);
        
        try {
            $order->update([
                'is_paid' => false,
                'payment_status' => Order::PAYMENT_REJECTED,
                'rejection_reason' => $request->rejection_reason
            ]);
            
            return redirect()->route('admin.orders')->with('success', 'Pembayaran ditolak! Customer akan diminta mengupload bukti pembayaran baru.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menolak pembayaran: ' . $e->getMessage());
        }
    }
}
