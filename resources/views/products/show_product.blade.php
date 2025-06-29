@extends('layouts.app')

@section('title', $product->name . ' - N-Shop')
@section('body-class', 'product-detail-page') <!-- Tambah class khusus -->

@section('content')
<div class="container"> <!-- Tetap pakai container biasa -->
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index_product') }}">Home</a></li>
            @if($product->categories->isNotEmpty())
                <li class="breadcrumb-item">
                    <a href="{{ route('product.by_category', $product->categories->first()) }}">
                        {{ $product->categories->first()->name }}
                    </a>
                </li>
            @endif
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-3">
        <!-- Product Images -->
        <div class="col-lg-5 mb-3">
            <div class="card card-custom">
                <div class="card-body p-0 mt-0">
                    <!-- Product Gallery -->
                    <div class="product-gallery">
                        <!-- Main Image -->
                        <div class="main-image-container">
                            @if($product->images && count($product->images) > 0)
                                <img src="{{ Storage::url($product->images[0]) }}" 
                                     class="product-main-image" 
                                     alt="{{ $product->name }}" 
                                     id="mainImage">
                            @else
                                <div class="product-main-image d-flex align-items-center justify-content-center bg-light">
                                    <i class="fas fa-image fa-4x text-muted"></i>
                                </div>
                            @endif
                            
                            <!-- Navigation Arrows -->
                            @if($product->images && count($product->images) > 1)
                                <button class="image-nav-btn prev-btn" onclick="prevImage()">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="image-nav-btn next-btn" onclick="nextImage()">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            @endif
                        </div>

                        <!-- Thumbnail Gallery - Full Width -->
                        @if($product->images && count($product->images) > 1)
                            <div class="thumbnail-container">
                                @foreach($product->images as $index => $image)
                                    <img src="{{ Storage::url($image) }}" 
                                         class="thumbnail-nav {{ $index === 0 ? 'active-thumb' : '' }}" 
                                         alt="{{ $product->name }}"
                                         data-index="{{ $index }}">
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-lg-7">
            <div class="card card-custom">
                <div class="card-body">
                    <!-- Product Name & Category dengan Wishlist -->
                    <div class="mb-3">
                        <!-- Kategori Produk -->
                        <div class="product-categories mb-3">
                            <span class="text-muted">Kategori:</span>
                            @foreach($product->categories as $index => $category)
                                <a href="{{ route('product.by_category', $category) }}" class="badge rounded-pill bg-light text-dark me-1">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                        
                        <!-- Nama Produk dan Wishlist sejajar -->
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="mb-0 text-primary">{{ $product->name }}</h2>
                            
                            <!-- Wishlist Button - Bentuk Lama -->
                            @auth
                                @if(!auth()->user()->is_admin)
                                    @php $isWishlisted = auth()->user()->wishlists->where('product_id', $product->id)->count() > 0; @endphp
                                    <button type="button" class="wishlist-btn {{ $isWishlisted ? 'active' : '' }}" 
                                            data-product-id="{{ $product->id }}">
                                        <i class="fas fa-heart {{ $isWishlisted ? 'text-danger' : 'text-muted' }}"></i>
                                    </button>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <!-- Rating -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center">
                            @php $avgRating = $product->getAverageRating(); @endphp
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $avgRating ? 'text-warning' : 'text-muted' }}"></i>
                            @endfor
                            <span class="ms-2 text-muted">
                                {{ number_format($avgRating, 1) }} 
                                ({{ $product->getTotalReviews() }} review)
                            </span>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="mb-4">
                        <h3 class="text-primary mb-0" id="productPrice">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </h3>
                        @if($product->hasVariants())
                            <small class="text-muted">*Harga dapat berubah sesuai varian</small>
                        @endif
                    </div>

                    <!-- Product Variants -->
                    @if($product->hasVariants())
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3">Pilih Varian:</h6>
                            <div class="variants-container">
                                @php
                                    $sizes = $product->variants->groupBy('size');
                                    $colors = $product->variants->groupBy('color');
                                @endphp

                                <!-- Variant Selection Form -->
                                <form id="variantForm" method="GET">
                                    <!-- Size Selection -->
                                    @if($sizes->count() > 1)
                                        <div class="mb-3">
                                            <label class="form-label">Ukuran:</label>
                                            <select name="size" class="form-select" onchange="this.form.submit()">
                                                <option value="">Pilih Ukuran</option>
                                                @foreach($sizes as $size => $variants)
                                                    <option value="{{ $size }}" {{ request('size') == $size ? 'selected' : '' }}>
                                                        {{ $size }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <!-- Color Selection -->
                                    @if($colors->count() > 1)
                                        <div class="mb-3">
                                            <label class="form-label">Warna:</label>
                                            <select name="color" class="form-select" onchange="this.form.submit()">
                                                <option value="">Pilih Warna</option>
                                                @foreach($colors as $color => $variants)
                                                    <option value="{{ $color }}" {{ request('color') == $color ? 'selected' : '' }}>
                                                        {{ $color }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                </form>

                                <!-- Show Selected Variant Info -->
                                @if(request('size') || request('color'))
                                    @php
                                        $selectedVariant = $product->variants->where('size', request('size'))->where('color', request('color'))->first();
                                    @endphp
                                    @if($selectedVariant)
                                        <div class="alert alert-info">
                                            <strong>Varian dipilih:</strong> {{ $selectedVariant->size }} - {{ $selectedVariant->color }}<br>
                                            <strong>Harga:</strong> Rp {{ number_format($selectedVariant->price, 0, ',', '.') }}<br>
                                            <strong>Stok:</strong> {{ $selectedVariant->stock }}
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Stock Info -->
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-6">
                                <span>Stok: </span> <!-- Hapus strong/bold -->
                                <span id="stockInfo">
                                    @if($product->stock > 0)
                                        <span class="text-success">{{ $product->stock }} tersedia</span>
                                    @else
                                        <span class="text-danger">Stok habis</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Add to Cart Form dengan Quantity Control -->
                    @auth
                        @if(!auth()->user()->is_admin)
                            <form action="{{ route('add_to_cart', $product) }}" method="POST" id="addToCartForm">
                                @csrf
                                
                                @php
                                    $selectedVariant = null;
                                    $finalPrice = $product->price;
                                    $finalStock = $product->stock;
                                    
                                    if((request('size') || request('color')) && $product->hasVariants()) {
                                        $selectedVariant = $product->variants->where('size', request('size'))->where('color', request('color'))->first();
                                        if($selectedVariant) {
                                            $finalPrice = $selectedVariant->price;
                                            $finalStock = $selectedVariant->stock;
                                        }
                                    }
                                @endphp
                                
                                @if($selectedVariant)
                                    <input type="hidden" name="product_variant_id" value="{{ $selectedVariant->id }}">
                                @endif
                                
                                <!-- Quantity Controls Sejajar dengan Label -->
                                <div class="mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <label class="form-label fw-semibold mb-0">Jumlah:</label>
                                        <div class="quantity-controls">
                                            <button type="button" class="qty-btn qty-decrease">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" name="amount" id="quantityInput" class="qty-input" 
                                                   value="1" min="1" max="{{ $finalStock }}">
                                            <button type="button" class="qty-btn qty-increase">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg" 
                                            {{ $finalStock <= 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-cart-plus me-2"></i>
                                        {{ $finalStock <= 0 ? 'Stok Habis' : 'Tambah ke Keranjang' }}
                                    </button>
                                </div>
                            </form>
                        @endif
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <a href="{{ route('login') }}">Login</a> untuk menambahkan produk ke keranjang
                        </div>
                    @endauth

                    <!-- Admin Actions - Edit & Delete Product -->
                    @auth
                        @if(auth()->user()->is_admin)
                            <div class="admin-actions mb-4">
                                <a href="{{ route('admin.edit_product', $product) }}" class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i>Edit Produk
                                </a>
                                <form action="{{ route('admin.delete_product', $product) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger ms-2" 
                                            onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                        <i class="fas fa-trash me-2"></i>Hapus Produk
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Product Description & Reviews -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-body">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs" id="productTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="description-tab" data-bs-toggle="tab" 
                                    data-bs-target="#description" type="button">
                                <i class="fas fa-info-circle me-2"></i>Deskripsi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" 
                                    data-bs-target="#reviews" type="button">
                                <i class="fas fa-star me-2"></i>Review ({{ $product->getTotalReviews() }})
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content mt-4" id="productTabContent">
                        <!-- Description Tab -->
                        <div class="tab-pane fade show active" id="description">
                            <div class="row">
                                <div class="col-lg-8">
                                    <h5>Deskripsi Produk</h5>
                                    <p>{{ $product->description ?: 'Tidak ada deskripsi tersedia.' }}</p>
                                </div>
                                <!-- Spesifikasi Produk -->
                                <div class="col-lg-4">
                                    <h6>Spesifikasi</h6>
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <td>Kategori</td>
                                                <td>
                                                    @if($product->categories->count() > 0)
                                                        <div class="d-flex flex-wrap gap-1">
                                                            @foreach($product->categories as $category)
                                                                <a href="{{ route('product.by_category', $category) }}" class="badge rounded-pill bg-light text-dark">
                                                                    {{ $category->name }}
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            @if($product->weight)
                                                <tr>
                                                    <td>Berat</td>
                                                    <td>{{ number_format((float)$product->weight, 3, '.', '') }} kg</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td>Stok</td>
                                                <td>{{ $product->stock }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Reviews Tab -->
                        <div class="tab-pane fade" id="reviews">
                            <!-- Reviews List -->
                            @if($product->reviews->count() > 0)
                                @foreach($product->reviews as $review)
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div class="d-flex">
                                                    @if($review->user->avatar ?? false)
                                                        <img src="{{ Storage::url($review->user->avatar) }}" 
                                                             class="rounded-circle me-3" width="50" height="50">
                                                    @else
                                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" 
                                                             style="width: 50px; height: 50px;">
                                                            <i class="fas fa-user text-white"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-1">{{ $review->user->name }}</h6>
                                                        <div class="mb-2">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                            @endfor
                                                        </div>
                                                        @if($review->comment)
                                                            <p class="mb-2">{{ $review->comment }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                    <h5>Belum ada review</h5>
                                    <p class="text-muted">Jadilah yang pertama memberikan review untuk produk ini!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h4 class="mb-4">Produk Serupa</h4>
                <div class="row">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card product-card h-100">
                                <div class="position-relative">
                                    @if($relatedProduct->getMainImage())
                                        <img src="{{ Storage::url($relatedProduct->getMainImage()) }}" 
                                             class="card-img-top product-image" alt="{{ $relatedProduct->name }}">
                                    @else
                                        <div class="card-img-top product-image d-flex align-items-center justify-content-center bg-light">
                                            <i class="fas fa-image fa-2x text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">{{ Str::limit($relatedProduct->name, 40) }}</h6>
                                    <div class="mb-2">
                                        @php $relatedRating = $relatedProduct->getAverageRating(); @endphp
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $relatedRating ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                    </div>
                                    <h6 class="text-primary">Rp {{ number_format($relatedProduct->price, 0, ',', '.') }}</h6>
                                    <a href="{{ route('show_product', $relatedProduct) }}" class="btn btn-primary btn-sm">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Cukup biarkan kosong, script akan dimuat dari file terpisah
</script>
@endpush