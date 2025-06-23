<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'UserOnly']);
    }

    public function index()
    {
        $wishlists = Wishlist::with(['product.category', 'product.reviews'])
                            ->where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->paginate(12);
        
        return view('user.user_wishlist', compact('wishlists'));
    }

    public function toggle(Product $product)
    {
        $user_id = Auth::id();
        
        $wishlist = Wishlist::where('user_id', $user_id)
                           ->where('product_id', $product->id)
                           ->first();
        
        if($wishlist) {
            $wishlist->delete();
            $message = 'Produk dihapus dari wishlist!';
            $status = 'removed';
        } else {
            Wishlist::create([
                'user_id' => $user_id,
                'product_id' => $product->id
            ]);
            $message = 'Produk ditambahkan ke wishlist!';
            $status = 'added';
        }
        
        if(request()->ajax()) {
            return response()->json([
                'status' => $status,
                'message' => $message
            ]);
        }
        
        return Redirect::back()->with('success', $message);
    }

    public function destroy(Wishlist $wishlist)
    {
        // Cek kepemilikan
        if($wishlist->user_id != Auth::id()) {
            return Redirect::route('wishlist.index')->with('error', 'Akses ditolak!');
        }

        $wishlist->delete();
        return Redirect::route('wishlist.index')->with('success', 'Item berhasil dihapus dari wishlist!');
    }

    public function landing()
    {
        // Jika ada halaman landing wishlist terpisah
        return view('user.wishlist');
    }
}