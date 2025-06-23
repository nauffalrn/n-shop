<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Product $product)
    {
        // Cek apakah user sudah pernah membeli produk ini
        $hasPurchased = Order::whereHas('transactions', function($query) use ($product) {
            $query->where('product_id', $product->id);
        })->where('user_id', Auth::id())
          ->where('is_paid', true)
          ->exists();

        if(!$hasPurchased) {
            return Redirect::back()->with('error', 'Anda harus membeli produk ini terlebih dahulu untuk memberikan review!');
        }

        // Cek apakah user sudah pernah review produk ini
        $existingReview = Review::where('user_id', Auth::id())
                               ->where('product_id', $product->id)
                               ->first();

        if($existingReview) {
            return Redirect::back()->with('error', 'Anda sudah memberikan review untuk produk ini!');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imagePath = null;
        if($request->hasFile('image')) {
            $file = $request->file('image');
            $imagePath = time() . '_review_' . uniqid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put('public/' . $imagePath, file_get_contents($file));
        }

        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'image' => $imagePath
        ]);

        // Update rating produk
        $averageRating = $product->getAverageRating();
        $product->update(['rating' => $averageRating]);

        return Redirect::back()->with('success', 'Review berhasil ditambahkan!');
    }

    public function destroy(Review $review)
    {
        // Cek kepemilikan review
        if($review->user_id != Auth::id()) {
            return Redirect::back()->with('error', 'Akses ditolak!');
        }

        // Hapus gambar jika ada
        if($review->image) {
            Storage::disk('local')->delete('public/' . $review->image);
        }

        $product = $review->product;
        $review->delete();

        // Update rating produk
        $averageRating = $product->getAverageRating();
        $product->update(['rating' => $averageRating]);

        return Redirect::back()->with('success', 'Review berhasil dihapus!');
    }
}