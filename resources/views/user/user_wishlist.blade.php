@extends('layouts.app')

@section('title', 'Wishlist - N-Shop')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-heart me-2"></i>Wishlist Saya
            </h2>
        </div>
    </div>

    @if($wishlists->count() > 0)
        <div class="row">
            @foreach($wishlists as $wishlist)
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            @if($wishlist->product->getMainImage())
                                <img src="{{ Storage::url($wishlist->product->getMainImage()) }}" 
                                     class="card-img-top product-image" alt="{{ $wishlist->product->name }}">
                            @else
                                <div class="card-img-top product-image d-flex align-items-center justify-content-center bg-light">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                            
                            <!-- Remove from Wishlist -->
                            <form action="{{ route('wishlist.destroy', $wishlist) }}" method="POST" 
                                  class="position-absolute top-0 end-0 m-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" 
                                        onclick="return confirm('Hapus dari wishlist?')"
                                        title="Hapus dari Wishlist">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>

                            <!-- Category Badge -->
                            @if($wishlist->product->category)
                                <span class="badge badge-custom position-absolute top-0 start-0 m-2">
                                    {{ $wishlist->product->category->name }}
                                </span>
                            @endif
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title mb-2">
                                <a href="{{ route('show_product', $wishlist->product) }}" 
                                   class="text-decoration-none">
                                    {{ Str::limit($wishlist->product->name, 50) }}
                                </a>
                            </h6>
                            
                            <!-- Rating -->
                            <div class="mb-2">
                                @php $rating = $wishlist->product->getAverageRating(); @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                                <small class="text-muted ms-1">
                                    ({{ $wishlist->product->getTotalReviews() }})
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
                                    <span class="badge bg-success">Tersedia</span>
                                @else
                                    <span class="badge bg-danger">Stok Habis</span>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="mt-auto">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('show_product', $wishlist->product) }}" 
                                       class="btn btn-primary">
                                        <i class="fas fa-eye me-2"></i>Lihat Detail
                                    </a>
                                    
                                    @if($wishlist->product->stock > 0)
                                        <form action="{{ route('add_to_cart', $wishlist->product) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="amount" value="1">
                                            <button type="submit" class="btn btn-outline-primary">
                                                <i class="fas fa-cart-plus me-2"></i>Tambah ke Keranjang
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <!-- Added Date -->
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    Ditambahkan {{ $wishlist->created_at->diffForHumans() }}
                                </small>
                            </div>
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