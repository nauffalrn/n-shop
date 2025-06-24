@extends('layouts.app')

@section('title', 'Wishlist - N-Shop')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-heart me-2 text-danger"></i>Wishlist Saya</h2>
                <a href="{{ route('index_product') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Lanjut Belanja
                </a>
            </div>
        </div>
    </div>

    @if($wishlists->count() > 0)
        <div class="row" id="productsGrid">
            @foreach($wishlists as $wishlist)
                <div class="col-lg-3 col-md-6 mb-4">
                    <!-- Product Card dengan hover untuk direct link -->
                    <div class="card product-card h-100 position-relative product-clickable" 
                         data-product-url="{{ route('show_product', $wishlist->product) }}"
                         style="cursor: pointer;">
                        
                        <!-- Wishlist Button -->
                        <button type="button" class="wishlist-btn active" 
                                data-product-id="{{ $wishlist->product->id }}"
                                onclick="event.stopPropagation();">
                            <i class="fas fa-heart text-danger"></i>
                        </button>

                        <!-- Product Image -->
                        <div class="product-image-container">
                            @if($wishlist->product->getMainImage())
                                <img src="{{ Storage::url($wishlist->product->getMainImage()) }}" 
                                     class="product-image-grid" 
                                     alt="{{ $wishlist->product->name }}">
                            @else
                                <div class="product-image-grid product-image-placeholder">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>

                        <div class="card-body">
                            <!-- Product Name -->
                            <h6 class="card-title">{{ Str::limit($wishlist->product->name, 50) }}</h6>

                            <!-- Product Category -->
                            @if($wishlist->product->category)
                                <small class="text-muted">{{ $wishlist->product->category->name }}</small>
                            @endif

                            <!-- Rating -->
                            <div class="mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $wishlist->product->rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                                <small class="text-muted ms-1">
                                    ({{ $wishlist->product->getTotalReviews() }} review)
                                </small>
                            </div>

                            <!-- Price -->
                            <div class="mb-3">
                                <h5 class="text-primary mb-0">
                                    Rp {{ number_format($wishlist->product->price, 0, ',', '.') }}
                                </h5>
                                @if($wishlist->product->hasVariants())
                                    <small class="text-muted">Mulai dari</small>
                                @endif
                            </div>

                            <!-- Stock Status -->
                            <div class="mb-3">
                                @if($wishlist->product->stock > 0)
                                    <span class="badge bg-success">Stok: {{ $wishlist->product->stock }}</span>
                                @else
                                    <span class="badge bg-danger">Stok Habis</span>
                                @endif
                            </div>

                            <!-- Quick Add to Cart -->
                            @if($wishlist->product->stock > 0)
                                <form action="{{ route('add_to_cart', $wishlist->product) }}" method="POST" 
                                      onclick="event.stopPropagation();">
                                    @csrf
                                    <input type="hidden" name="amount" value="1">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-cart-plus me-2"></i>Tambah ke Keranjang
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $wishlists->links() }}
        </div>
    @else
        <!-- Empty Wishlist -->
        <div class="text-center py-5">
            <i class="fas fa-heart-broken fa-4x text-muted mb-4"></i>
            <h4>Wishlist Masih Kosong</h4>
            <p class="text-muted mb-4">Belum ada produk yang ditambahkan ke wishlist</p>
            <a href="{{ route('index_product') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-bag me-2"></i>Mulai Belanja
            </a>
        </div>
    @endif
</div>
@endsection