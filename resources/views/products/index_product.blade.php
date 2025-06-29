@extends('layouts.app')

@section('title', 'Produk - N-Shop')

@section('content')
<div class="container-fluid px-0">
    <!-- Main Content -->
    <div class="row g-0">
        <!-- Sidebar Filter -->
        <div class="col-lg-2 ps-4 pe-2 pt-0">
            <div class="card card-custom">
                <div class="card-header" style="background-color: var(--primary-color); color: white;">
                    <h6 class="mb-0 text-white fw-semibold"><i class="fas fa-filter me-2"></i>Filter Produk</h6>
                </div>
                <div class="card-body">
                    <form action="{{ isset($category) ? route('product.by_category', $category) : route('index_product') }}" method="GET" id="filterForm">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        @if(isset($category))
                            <input type="hidden" name="category" value="{{ $category->id }}">
                        @endif
                        
                        <!-- Filter Kategori -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-tags me-1"></i>Kategori
                            </label>
                            <select name="category_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" 
                                            {{ (request('category_id') == $cat->id || (isset($category) && $category->id == $cat->id)) ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Harga -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-dollar-sign me-1"></i>Range Harga
                            </label>
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
                            <label class="form-label fw-semibold">
                                <i class="fas fa-sort me-1"></i>Urutkan
                            </label>
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
                <div class="card-header" style="background-color: var(--primary-color); color: white;">
                    <h6 class="mb-0 text-white fw-semibold"><i class="fas fa-tags me-2"></i>Kategori Populer</h6>
                </div>
                <div class="card-body">
                    <!-- Category links -->
                    @foreach($categories->take(6) as $cat)
                        <a href="{{ route('product.by_category', $cat) }}" 
                           class="d-block text-decoration-none mb-2 p-2 rounded hover-bg {{ (isset($category) && $category->id == $cat->id) ? 'bg-primary text-white' : '' }}">
                            <i class="fas fa-chevron-right me-2 {{ (isset($category) && $category->id == $cat->id) ? 'text-white' : 'text-primary' }}"></i>
                            {{ $cat->name }}
                            <span class="badge {{ (isset($category) && $category->id == $cat->id) ? 'bg-light text-primary' : 'bg-secondary' }} ms-2">
                                {{ $cat->products_count ?? 0 }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9 pe-4 ps-2 pt-0">
            <!-- Category/Search Header -->
            <div class="mb-4">
                <!-- Active Filters -->
                @if(request()->hasAny(['category_id', 'min_price', 'max_price', 'sort', 'search']) || isset($category))
                    <div class="mt-3">
                        <span class="text-muted me-2">Filter aktif:</span>
                        
                        @if(isset($category))
                            <span class="badge bg-primary me-2">
                                {{ $category->name }}
                                <a href="{{ route('index_product') }}" class="text-white ms-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endif
                        
                        @if(request('search'))
                            <span class="badge bg-info me-2">
                                "{{ request('search') }}"
                                <a href="{{ request()->fullUrlWithQuery(['search' => '']) }}" class="text-white ms-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endif
                        
                        @if(request('min_price') || request('max_price'))
                            <span class="badge bg-warning me-2">
                                Harga: Rp {{ number_format(request('min_price', 0), 0, ',', '.') }} - Rp {{ number_format(request('max_price', 999999999), 0, ',', '.') }}
                                <a href="{{ request()->fullUrlWithQuery(['min_price' => '', 'max_price' => '']) }}" class="text-white ms-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endif
                        
                        @if(request('sort'))
                            <span class="badge bg-secondary me-2">
                                Urutan: {{ 
                                    request('sort') == 'price_low' ? 'Harga Terendah' : 
                                    (request('sort') == 'price_high' ? 'Harga Tertinggi' : 
                                    (request('sort') == 'rating' ? 'Rating Tertinggi' : 
                                    (request('sort') == 'newest' ? 'Terbaru' : 'Default')))
                                }}
                                <a href="{{ request()->fullUrlWithQuery(['sort' => '']) }}" class="text-white ms-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endif
                        
                        <a href="{{ route('index_product') }}" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-trash me-1"></i>Hapus Semua Filter
                        </a>
                    </div>
                @endif
            </div>
            
            <!-- Products Grid -->
            @if($products->count() > 0)
                <div class="row" id="productsGrid">
                    @foreach($products as $product)
                        <div class="col-lg-3 col-md-6 mb-4">
                            <!-- Product Card dengan hover untuk direct link -->
                            <div class="card product-card h-100 position-relative product-clickable" 
                                 data-product-url="{{ url('/product/' . $product->id) }}"
                                 style="cursor: pointer;">
                                
                                <!-- Wishlist Button -->
                                @auth
                                    @if(!auth()->user()->is_admin)
                                        @php $isWishlisted = auth()->user()->wishlists->where('product_id', $product->id)->count() > 0; @endphp
                                        <button type="button" class="wishlist-btn {{ $isWishlisted ? 'active' : '' }}" 
                                                data-product-id="{{ $product->id }}"
                                                onclick="event.stopPropagation();">
                                            <i class="fas fa-heart {{ $isWishlisted ? 'text-danger' : 'text-muted' }}"></i>
                                        </button>
                                    @endif
                                @endauth

                                <!-- Product Image -->
                                <div class="product-image-container">
                                    @if($product->getMainImage())
                                        <img src="{{ Storage::url($product->getMainImage()) }}" 
                                             class="product-image-grid" 
                                             alt="{{ $product->name }}">
                                    @else
                                        <div class="product-image-grid product-image-placeholder">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="card-body">
                                    <!-- Product Name -->
                                    <h6 class="card-title">{{ Str::limit($product->name, 50) }}</h6>

                                    <!-- Product Category -->
                                    @if($product->category)
                                        <small class="text-muted">{{ $product->category->name }}</small>
                                    @endif

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
                                    </div>

                                    <!-- Quick Add to Cart -->
                                    @auth
                                        @if(!auth()->user()->is_admin && $product->stock > 0)
                                            <form action="{{ route('add_to_cart', $product) }}" method="POST" 
                                                  onclick="event.stopPropagation();">
                                                @csrf
                                                <input type="hidden" name="amount" value="1">
                                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                                    <i class="fas fa-cart-plus me-2"></i>Tambah ke Keranjang
                                                </button>
                                            </form>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pagination-container">
                    {{ $products->appends(request()->query())->links('products.pagination') }}
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Product clickable
    $('.product-clickable').on('click', function() {
        window.location.href = $(this).data('product-url');
    });
});
</script>
@endpush