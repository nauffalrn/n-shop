@extends('layouts.app')

@section('title', 'Produk - N-Shop')

@section('content')
<div class="container-fluid">
    <!-- Hero Section dengan Search -->
    <div class="hero-section py-5 mb-4" style="background: linear-gradient(135deg, var(--accent-color) 0%, var(--light-color) 100%);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 text-center text-lg-start">
                    <h1 class="fw-bold text-primary mb-3 hero-title">
                        Selamat Datang di N-Shop!
                    </h1>
                    <p class="lead text-muted mb-4">Temukan produk terbaik dengan harga terjangkau dan kualitas terpercaya</p>
                </div>
                <div class="col-lg-6">
                    <!-- Search Form Mobile -->
                    <div class="d-lg-none mb-3">
                        <form action="{{ route('index_product') }}" method="GET">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control search-input" 
                                       placeholder="Cari produk..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Sidebar Filter -->
            <div class="col-lg-3 mb-4">
                <div class="card card-custom">
                    <div class="card-header" style="background-color: var(--primary-color); color: white;">
                        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Produk</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('index_product') }}" method="GET" id="filterForm">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            
                            <!-- Filter Kategori -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Kategori</label>
                                <select name="category_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" 
                                                {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Harga -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Range Harga</label>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="number" name="min_price" class="form-control" 
                                               placeholder="Min" value="{{ request('min_price') }}">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" name="max_price" class="form-control" 
                                               placeholder="Max" value="{{ request('max_price') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Sort -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Urutkan</label>
                                <select name="sort" class="form-select" onchange="this.form.submit()">
                                    <option value="">Default</option>
                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>
                                        Harga Terendah
                                    </option>
                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>
                                        Harga Tertinggi
                                    </option>
                                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>
                                        Rating Tertinggi
                                    </option>
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>
                                        Terbaru
                                    </option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Terapkan Filter
                            </button>
                            
                            @if(request()->hasAny(['category_id', 'min_price', 'max_price', 'sort', 'search']))
                                <a href="{{ route('index_product') }}" class="btn btn-outline-secondary w-100 mt-2">
                                    <i class="fas fa-times me-2"></i>Reset Filter
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Quick Categories -->
                <div class="card card-custom mt-3">
                    <div class="card-header" style="background-color: var(--secondary-color); color: white;">
                        <h6 class="mb-0"><i class="fas fa-tags me-2"></i>Kategori Populer</h6>
                    </div>
                    <div class="card-body">
                        <!-- Category links -->
                        @foreach($categories->take(6) as $category)
                            <a href="{{ route('product.by_category', $category) }}" 
                               class="d-block text-decoration-none mb-2 p-2 rounded hover-bg">
                                <i class="fas fa-chevron-right me-2 text-primary"></i>
                                {{ $category->name }}
                                <span class="badge bg-secondary ms-2">{{ $category->products_count ?? 0 }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-lg-9">
                <!-- Results Info -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1">
                            @if(isset($category))
                                Produk dalam {{ $category->name }}
                            @elseif(request('search'))
                                Hasil pencarian "{{ request('search') }}"
                            @else
                                Semua Produk
                            @endif
                        </h4>
                        <p class="text-muted mb-0">Ditemukan {{ $products->total() }} produk</p>
                    </div>
                    
                    <!-- View Toggle -->
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary active" id="gridView">
                            <i class="fas fa-th"></i>
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="listView">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>

                <!-- Products -->
                @if($products->count() > 0)
                    <div class="row" id="productsContainer">
                        @foreach($products as $product)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card product-card h-100">
                                    <!-- Product Image -->
                                    <div class="position-relative">
                                        @if($product->getMainImage())
                                            <img src="{{ Storage::url($product->getMainImage()) }}" 
                                                 class="card-img-top product-image" alt="{{ $product->name }}">
                                        @else
                                            <div class="card-img-top product-image d-flex align-items-center justify-content-center bg-light">
                                                <i class="fas fa-image fa-3x text-muted"></i>
                                            </div>
                                        @endif
                                        
                                        <!-- Wishlist Button -->
                                        @auth
                                            @if(!auth()->user()->is_admin)
                                                <button class="btn btn-light position-absolute top-0 end-0 m-2 wishlist-btn" 
                                                        data-product-id="{{ $product->id }}">
                                                    <i class="fas fa-heart {{ auth()->user()->wishlists->where('product_id', $product->id)->count() > 0 ? 'text-danger' : 'text-muted' }}"></i>
                                                </button>
                                            @endif
                                        @endauth

                                        <!-- Category Badge -->
                                        @if($product->category)
                                            <span class="badge badge-custom position-absolute top-0 start-0 m-2">
                                                {{ $product->category->name }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="card-body d-flex flex-column">
                                        <h6 class="card-title mb-2">{{ Str::limit($product->name, 50) }}</h6>
                                        
                                        <!-- Rating -->
                                        <div class="mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $product->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                            <small class="text-muted ms-1">
                                                ({{ $product->getTotalReviews() }} review)
                                            </small>
                                        </div>

                                        <!-- Price -->
                                        <div class="mb-3">
                                            <h5 class="text-primary mb-0">Rp {{ number_format($product->price, 0, ',', '.') }}</h5>
                                            @if($product->hasVariants())
                                                <small class="text-muted">Mulai dari</small>
                                            @endif
                                        </div>

                                        <!-- Stock Status -->
                                        <div class="mb-3">
                                            @if($product->stock > 0)
                                                <span class="badge bg-success">Stok: {{ $product->stock }}</span>
                                            @else
                                                <span class="badge bg-danger">Stok Habis</span>
                                            @endif
                                        </div>

                                        <!-- Actions -->
                                        <div class="mt-auto">
                                            <div class="d-grid gap-2">
                                                <a href="{{ route('show_product', $product) }}" 
                                                   class="btn btn-primary">
                                                    <i class="fas fa-eye me-2"></i>Lihat Detail
                                                </a>
                                                
                                                @auth
                                                    @if(!auth()->user()->is_admin && $product->stock > 0)
                                                        <form action="{{ route('add_to_cart', $product) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="amount" value="1">
                                                            <button type="submit" class="btn btn-outline-primary">
                                                                <i class="fas fa-cart-plus me-2"></i>Tambah ke Keranjang
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    <!-- Admin edit product link -->
                                                    @if(auth()->user()->is_admin)
                                                        <a href="{{ route('admin.edit_product', $product) }}" 
                                                           class="btn btn-outline-warning">
                                                            <i class="fas fa-edit me-2"></i>Edit Produk
                                                        </a>
                                                    @endif
                                                @endauth
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4>Tidak ada produk ditemukan</h4>
                        <p class="text-muted">Coba ubah filter atau kata kunci pencarian Anda</p>
                        <a href="{{ route('index_product') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Lihat Semua Produk
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Wishlist functionality untuk user saja
    $('.wishlist-btn').on('click', function() {
        const productId = $(this).data('product-id');
        
        $.ajax({
            url: '/wishlist/' + productId + '/toggle',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.status === 'added') {
                    $('.wishlist-btn[data-product-id="' + productId + '"] i')
                        .removeClass('text-muted').addClass('text-danger');
                } else {
                    $('.wishlist-btn[data-product-id="' + productId + '"] i')
                        .removeClass('text-danger').addClass('text-muted');
                }
            }
        });
    });
});
</script>
@endpush