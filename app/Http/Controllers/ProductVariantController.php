<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Redirect;

class ProductVariantController extends Controller
{
    public function __construct()
    {
        $this->middleware('Admin');
    }

    public function index(Product $product)
    {
        $variants = $product->variants;
        return view('admin.products.variants.variant_index', compact('product', 'variants'));
    }

    public function create(Product $product)
    {
        return view('admin.products.variants.variant_create', compact('product'));
    }

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'size' => 'required|string|max:50',
            'color' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0'
        ]);

        // Cek apakah kombinasi size dan color sudah ada
        $existingVariant = ProductVariant::where('product_id', $product->id)
                                      ->where('size', $request->size)
                                      ->where('color', $request->color)
                                      ->first();

        if($existingVariant) {
            return Redirect::back()
                          ->withInput()
                          ->with('error', 'Variant dengan ukuran dan warna yang sama sudah ada!');
        }

        ProductVariant::create([
            'product_id' => $product->id,
            'size' => $request->size,
            'color' => $request->color,
            'price' => $request->price,
            'stock' => $request->stock
        ]);

        return Redirect::route('admin.product.variants.index', $product)
                      ->with('success', 'Variant berhasil ditambahkan!');
    }

    public function edit(Product $product, ProductVariant $variant)
    {
        // Pastikan variant milik product yang benar
        if($variant->product_id != $product->id) {
            return Redirect::route('admin.product.variants.index', $product) // Updated
                          ->with('error', 'Variant tidak ditemukan!');
        }

        return view('admin.products.variants.variant_edit', compact('product', 'variant')); // Fixed
    }

    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        // Pastikan variant milik product yang benar
        if($variant->product_id != $product->id) {
            return Redirect::route('admin.product.variants.index', $product) // Updated
                          ->with('error', 'Variant tidak ditemukan!');
        }

        $request->validate([
            'size' => 'required|string|max:50',
            'color' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0'
        ]);

        // Cek apakah kombinasi size dan color sudah ada (kecuali untuk variant ini sendiri)
        $existingVariant = ProductVariant::where('product_id', $product->id)
                                      ->where('size', $request->size)
                                      ->where('color', $request->color)
                                      ->where('id', '!=', $variant->id)
                                      ->first();

        if($existingVariant) {
            return Redirect::back()
                          ->withInput()
                          ->with('error', 'Variant dengan ukuran dan warna yang sama sudah ada!');
        }

        $variant->update([
            'size' => $request->size,
            'color' => $request->color,
            'price' => $request->price,
            'stock' => $request->stock
        ]);

        return Redirect::route('admin.product.variants.index', $product)
                      ->with('success', 'Variant berhasil diupdate!');
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        // Pastikan variant milik product yang benar
        if($variant->product_id != $product->id) {
            return Redirect::route('admin.product.variants.index', $product) // Updated
                          ->with('error', 'Variant tidak ditemukan!');
        }

        // Cek apakah ada item di cart yang menggunakan variant ini
        if($variant->carts()->count() > 0) {
            return Redirect::route('admin.product.variants.index', $product) // Updated
                          ->with('error', 'Variant tidak dapat dihapus karena masih ada di keranjang pengguna!');
        }

        $variant->delete();

        return Redirect::route('admin.product.variants.index', $product)
                      ->with('success', 'Variant berhasil dihapus!');
    }
}