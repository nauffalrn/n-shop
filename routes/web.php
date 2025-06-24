<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes (dapat diakses semua orang)
Route::get('/', [ProductController::class, 'index_product'])->name('index_product');
Route::get('/product', [ProductController::class, 'index_product'])->name('index_product');
Route::get('/product/{product}', [ProductController::class, 'show_product'])->name('show_product');
Route::get('/category/{category}', [ProductController::class, 'by_category'])->name('product.by_category'); 

// Auth Routes
Auth::routes();

// Redirect /home berdasarkan role
Route::get('/home', function() {
    if(auth()->check() && auth()->user()->is_admin) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('index_product');
});

// USER ONLY Routes (hanya untuk user biasa, bukan admin)
Route::middleware(['auth', 'UserOnly'])->group(function () {
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show_profile'])->name('show_profile');
    Route::patch('/profile', [ProfileController::class, 'edit_profile'])->name('edit_profile');
    
    // Cart Routes  
    Route::get('/cart', [CartController::class, 'show_cart'])->name('show_cart');
    Route::post('/cart/{product}', [CartController::class, 'add_to_cart'])->name('add_to_cart');
    Route::patch('/cart/{cart}', [CartController::class, 'update_cart'])->name('update_cart');
    Route::delete('/cart/{cart}', [CartController::class, 'delete_cart'])->name('delete_cart');
    
    // Order Routes
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::get('/orders', [OrderController::class, 'index_order'])->name('index_order');
    Route::get('/orders/{order}', [OrderController::class, 'show_order'])->name('show_order');
    Route::post('/orders/{order}/payment', [OrderController::class, 'submit_payment_receipt'])->name('submit_payment_receipt');
    
    // Wishlist Routes
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    
    // Address Routes
    Route::resource('address', AddressController::class);
    
    // Review Routes
    Route::post('/product/{product}/review', [ReviewController::class, 'store'])->name('review.store');
    Route::delete('/review/{review}', [ReviewController::class, 'destroy'])->name('review.destroy');
});

// ADMIN ONLY Routes (hanya untuk admin)
Route::middleware(['auth', 'Admin'])->prefix('admin')->group(function () {
    
    // Admin Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Product Management
    Route::get('/product/create', [ProductController::class, 'create_product'])->name('admin.create_product');
    Route::post('/product', [ProductController::class, 'store_product'])->name('admin.store_product');
    Route::get('/product/{product}/edit', [ProductController::class, 'edit_product'])->name('admin.edit_product'); 
    Route::patch('/product/{product}', [ProductController::class, 'update_product'])->name('admin.update_product');
    Route::delete('/product/{product}', [ProductController::class, 'delete_product'])->name('admin.delete_product');
    
    // Category Management
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.category.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('admin.category.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.category.store');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('admin.category.show');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('admin.category.edit');
    Route::patch('/categories/{category}', [CategoryController::class, 'update'])->name('admin.category.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.category.destroy');
    
    // Product Variant Management
    Route::get('/product/{product}/variants', [ProductVariantController::class, 'index'])->name('admin.product.variants.index');
    Route::get('/product/{product}/variants/create', [ProductVariantController::class, 'create'])->name('admin.product.variants.create');
    Route::post('/product/{product}/variants', [ProductVariantController::class, 'store'])->name('admin.product.variants.store');
    Route::get('/product/{product}/variants/{variant}/edit', [ProductVariantController::class, 'edit'])->name('admin.product.variants.edit');
    Route::patch('/product/{product}/variants/{variant}', [ProductVariantController::class, 'update'])->name('admin.product.variants.update');
    Route::delete('/product/{product}/variants/{variant}', [ProductVariantController::class, 'destroy'])->name('admin.product.variants.destroy');
    
    // Order Management (Admin)
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::post('/orders/{order}/confirm', [OrderController::class, 'confirm_payment'])->name('admin.confirm_payment');
    
    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
});