<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class ProductController extends Controller
{
    public function create_product(){
        $categories = Category::all();
        return view('admin.products.create_product', compact('categories'));
    }

    public function store_product(Request $request){
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'description' => 'required',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
            'weight' => 'nullable|numeric'
        ]);

        $imagePaths = [];
        if($request->hasFile('images')) {
            foreach($request->file('images') as $image) {
                $path = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                Storage::disk('local')->put('public/' . $path, file_get_contents($image));
                $imagePaths[] = $path;
            }
        }

        Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description,
            'images' => $imagePaths,
            'category_id' => $request->category_id,
            'weight' => $request->weight
        ]);

        return Redirect::route('admin.create_product')->with('success', 'Produk berhasil ditambahkan!'); // Fixed route
    }

    public function index_product(Request $request)
    {
        $query = Product::with(['category', 'reviews']);
        
        // Search
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        
        // Filter by category
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        // Filter by price range
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }
        
        // Sorting
        switch ($request->sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('id', 'desc');
        }
        
        $products = $query->paginate(12);
        $categories = \App\Models\Category::withCount('products')->get();
        
        return view('products.index_product', compact('products', 'categories')); // ✅ Sudah benar
    }

    public function show_product(Product $product)
    {
        try {
            // Load relationships dengan error handling
            $product->load([
                'category', 
                'variants', 
                'reviews' => function($query) {
                    $query->with('user')->orderBy('created_at', 'desc');
                }
            ]);

            // Get related products dengan error handling
            $relatedProducts = collect();
            if($product->category_id) {
                $relatedProducts = Product::where('category_id', $product->category_id)
                                    ->where('id', '!=', $product->id)
                                    ->take(4)
                                    ->get();
            }

            return view('products.show_product', compact('product', 'relatedProducts')); // ✅ Sudah benar
        } catch (\Exception $e) {
            return redirect()->route('index_product')->with('error', 'Produk tidak ditemukan!');
        }
    }

    public function edit_prpoduct(Product $product){
        $categories = Category::all();
        return view('admin.products.edit_product', compact('product', 'categories')); // ✅ Sudah benar
    }

    public function update_product(Product $product, Request $request){
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'description' => 'required',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
            'weight' => 'nullable|numeric'
        ]);

        $imagePaths = $product->images ?? [];
        
        if($request->hasFile('images')) {
            // Hapus gambar lama
            if($product->images) {
                foreach($product->images as $oldImage) {
                    Storage::disk('local')->delete('public/' . $oldImage);
                }
            }
            
            // Upload gambar baru
            $imagePaths = [];
            foreach($request->file('images') as $image) {
                $path = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                Storage::disk('local')->put('public/' . $path, file_get_contents($image));
                $imagePaths[] = $path;
            }
        }

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description,
            'images' => $imagePaths,
            'category_id' => $request->category_id,
            'weight' => $request->weight
        ]);

        return Redirect::route('show_product', $product)->with('success', 'Produk berhasil diupdate!');
    }

    public function delete_product(Product $product){
        // Hapus gambar
        if($product->images) {
            foreach($product->images as $image) {
                Storage::disk('local')->delete('public/' . $image);
            }
        }
        
        $product->delete();
        return Redirect::route('index_product')->with('success', 'Produk berhasil dihapus!');
    }
    
    // Method untuk filter berdasarkan kategori
    public function by_category(Category $category) {
        // Build query dengan filter kategori
        $query = Product::where('category_id', $category->id);
        
        // Apply additional filters if exist
        if (request('min_price')) {
            $query->where('price', '>=', request('min_price'));
        }
        
        if (request('max_price')) {
            $query->where('price', '<=', request('max_price'));
        }
        
        // Apply sorting
        switch (request('sort')) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('id', 'desc');
        }
        
        $products = $query->paginate(12);
        $categories = Category::withCount('products')->get();
        
        return view('products.index_product', compact('products', 'categories', 'category'));
    }
}