@extends('layouts.app')

@section('title', $product->name . ' - N-Shop')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-6 mb-4">
            <!-- Product Images -->
            <div class="product-images">
                @if($product->images && count($product->images) > 0)
                    <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($product->images as $index => $image)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                    <img src="{{ Storage::url($image) }}" class="d-block w-100 product-main-image" alt="{{ $product->name }}">
                                </div>
                            @endforeach
                        </div>
                        
                        @if(count($product->images) > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        @endif
                    </div>
                    
                    <!-- Thumbnail Navigation -->
                    @if(count($product->images) > 1)
                        <div class="row mt-3">
                            @foreach($product->images as $index => $image)
                                <div class="col-3">
                                    <img src="{{ Storage::url($image) }}" 
                                         class="img-thumbnail cursor-pointer thumbnail-nav {{ $index == 0 ? 'active-thumb' : '' }}" 
                                         data-bs-target="#productCarousel" 
                                         data-bs-slide-to="{{ $index }}" 
                                         alt="Thumbnail {{ $index + 1 }}">
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="product-no-image d-flex align-items-center justify-content-center">
                        <i class="fas fa-image fa-5x text-muted"></i>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-6">
            <!-- Product Info -->
            <div class="product-details">
                <div class="mb-3">
                    @if($product->category)
                        <span class="badge badge-custom mb-2">{{ $product->category->name }}</span>
                    @endif
                    <h1 class="product-title">{{ $product->name }}</h1>
                </div>

                <!-- Rating -->
                <div class="product-rating mb-3">
                    @php $rating = $product->getAverageRating(); @endphp
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= $rating ? 'text-warning' : 'text-muted' }}"></i>
                    @endfor
                    <span class="ms-2 text-muted">{{ $rating }}/5 ({{ $product->getTotalReviews() }} reviews)</span>
                </div>

                <!-- Price -->
                <div class="product-price mb-4">
                    <h2 class="text-primary mb-0">Rp {{ number_format($product->price, 0, ',', '.') }}</h2>
                    @if($product->hasVariants())
                        <small class="text-muted">Harga mulai dari</small>
                    @endif
                </div>

                <!-- Stock -->
                <div class="product-stock mb-4">
                    @if($product->stock > 0)
                        <span class="badge bg-success">Tersedia</span>
                        <span class="text-muted ms-2">Stok: {{ $product->stock }}</span>
                    @else
                        <span class="badge bg-danger">Stok Habis</span>
                    @endif
                </div>

                <!-- Product Variants -->
                @if($product->hasVariants())
                    <div class="product-variants mb-4">
                        <h6>Pilih Variant:</h6>
                        <div class="variants-container">
                            @foreach($product->variants as $variant)
                                @if($variant->stock > 0)
                                    <div class="variant-option" data-variant-id="{{ $variant->id }}" 
                                         data-price="{{ $variant->price }}" data-stock="{{ $variant->stock }}">
                                        <span class="variant-info">
                                            {{ $variant->getVariantName() }} - Rp {{ number_format($variant->price, 0, ',', '.') }}
                                        </span>
                                        <small class="text-muted d-block">Stok: {{ $variant->stock }}</small>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Add to Cart Form -->
                @if($product->stock > 0 || $product->hasVariants())
                    @auth
                        <form action="{{ route('add_to_cart', $product) }}" method="POST" id="addToCartForm">
                            @csrf
                            <input type="hidden" name="product_variant_id" id="selectedVariantId">
                            
                            <div class="row mb-3">
                                <div class="col-4">
                                    <label for="amount" class="form-label">Jumlah:</label>
                                    <input type="number" class="form-control" id="amount" name="amount" 
                                           value="1" min="1" max="{{ $product->stock }}" required>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex">
                                <button type="submit" class="btn btn-primary btn-lg me-md-2">
                                    <i class="fas fa-cart-plus me-2"></i>Tambah ke Keranjang
                                </button>
                                
                                <button type="button" class="btn btn-outline-danger btn-lg wishlist-btn" 
                                        data-product-id="{{ $product->id }}">
                                    <i class="fas fa-heart me-2"></i>Wishlist
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="d-grid">
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Login untuk Membeli
                            </a>
                        </div>
                    @endauth
                @endif

                <!-- Product Description -->
                <div class="product-description mt-4">
                    <h6>Deskripsi Produk:</h6>
                    <p class="text-muted">{{ $product->description }}</p>
                </div>

                <!-- Product Details -->
                <div class="product-details-table mt-4">
                    <h6>Detail Produk:</h6>
                    <table class="table table-sm">
                        <tr>
                            <td width="30%">Kategori</td>
                            <td>{{ $product->category->name ?? 'Tidak ada kategori' }}</td>
                        </tr>
                        <tr>
                            <td>Berat</td>
                            <td>{{ $product->weight ? $product->weight . ' gram' : 'Tidak diketahui' }}</td>
                        </tr>
                        <tr>
                            <td>Stok</td>
                            <td>{{ $product->stock }} unit</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="related-products mt-5">
            <h4 class="mb-4">Produk Terkait</h4>
            <div class="row">
                @foreach($relatedProducts as $related)
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card product-card h-100">
                            <div class="position-relative">
                                @if($related->getMainImage())
                                    <img src="{{ Storage::url($related->getMainImage()) }}" 
                                         class="card-img-top product-image" alt="{{ $related->name }}">
                                @else
                                    <div class="card-img-top product-image d-flex align-items-center justify-content-center bg-light">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title">{{ Str::limit($related->name, 40) }}</h6>
                                <div class="mb-2">
                                    @php $relatedRating = $related->getAverageRating(); @endphp
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $relatedRating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                                <h5 class="text-primary mb-3">Rp {{ number_format($related->price, 0, ',', '.') }}</h5>
                                <div class="mt-auto">
                                    <a href="{{ route('show_product', $related) }}" class="btn btn-outline-primary btn-sm">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Variant selection
    $('.variant-option').on('click', function() {
        $('.variant-option').removeClass('selected');
        $(this).addClass('selected');
        
        const variantId = $(this).data('variant-id');
        const price = $(this).data('price');
        const stock = $(this).data('stock');
        
        $('#selectedVariantId').val(variantId);
        $('#amount').attr('max', stock);
        $('.product-price h2').text('Rp ' + price.toLocaleString('id-ID'));
    });

    // Wishlist functionality
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
                    $('.wishlist-btn i').removeClass('far').addClass('fas text-danger');
                    $('.wishlist-btn').removeClass('btn-outline-danger').addClass('btn-danger');
                } else {
                    $('.wishlist-btn i').removeClass('fas text-danger').addClass('far');
                    $('.wishlist-btn').removeClass('btn-danger').addClass('btn-outline-danger');
                }
            }
        });
    });

    // Enhanced thumbnail navigation
    $('.thumbnail-nav').on('click', function() {
        $('.thumbnail-nav').removeClass('active-thumb');
        $(this).addClass('active-thumb');
    });
});
</script>
@endpush
@endsection