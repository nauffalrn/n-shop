<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Redirect;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('Admin');
    }

    public function index()
    {
        $categories = Category::withCount('products')->get();
        return view('admin.categories.index_category', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create_category');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name'
        ]);

        Category::create([
            'name' => $request->name
        ]);

        return Redirect::route('admin.category.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit_category', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id
        ]);

        $category->update([
            'name' => $request->name
        ]);

        return Redirect::route('admin.category.index')->with('success', 'Kategori berhasil diupdate!');
    }

    public function destroy(Category $category)
    {
        // Cek apakah ada produk yang menggunakan kategori ini
        if($category->products()->count() > 0) {
            return Redirect::route('admin.category.index')->with('error', 'Kategori tidak dapat dihapus karena masih ada produk yang menggunakan kategori ini!'); // Fixed route
        }

        $category->delete();
        return Redirect::route('admin.category.index')->with('success', 'Kategori berhasil dihapus!');
    }
}