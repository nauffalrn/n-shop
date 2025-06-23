@extends('layouts.app')

@section('title', 'Keranjang Belanja - N-Shop')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-shopping-cart me-2"></i>Keranjang Belanja
            </h2>
        </div>
    </div>

    @if($carts->count() > 0)
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-custom">
                    <div class="card-body">
                        @foreach($carts as $cart)
                            <div class="cart-item d-flex align-items-center border-bottom py-3">
                                <!-- Product Image -->
                                <div class="cart-image me-3">
                                    @if($cart->product->getMainImage())
                                        <img src="{{ Storage::url($cart->product->getMainImage()) }}" 
                                             class="img-thumbnail" width="80" height="80" alt="{{ $cart->product->name }}">
                                    @else
                                        <div class="img-thumbnail d-flex align-items-center justify-content-center" 
                                             style="width: 80px; height: 80px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </div>

                                <!-- Product Info -->
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('show_product', $cart->product) }}" 
                                           class="text-decoration-none">
                                            {{ $cart->product->name }}
                                        </a>
                                    </h6>
                                    
                                    @if($cart->variant)
                                        <small class="text-muted">
                                            <i class="fas fa-tag me-1"></i>
                                            {{ $cart->variant->getVariantName() }}
                                        </small>
                                    @endif
                                    
                                    <div class="mt-2">
                                        <span class="fw-bold text-primary">
                                            Rp {{ number_format($cart->getFinalPrice(), 0, ',', '.') }}
                                        </span>
                                        <small class="text-muted">/ item</small>
                                    </div>
                                </div>

                                <!-- Quantity Controls -->
                                <div class="cart-quantity me-3">
                                    <form action="{{ route('update_cart', $cart) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <div class="input-group" style="width: 120px;">
                                            <input type="number" class="form-control text-center" 
                                                   name="amount" value="{{ $cart->amount }}" 
                                                   min="1" max="{{ $cart->variant ? $cart->variant->stock : $cart->product->stock }}">
                                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Total Price -->
                                <div class="cart-total me-3">
                                    <div class="fw-bold text-success">
                                        Rp {{ number_format($cart->getTotalPrice(), 0, ',', '.') }}
                                    </div>
                                </div>

                                <!-- Remove Button -->
                                <div class="cart-actions">
                                    <form action="{{ route('delete_cart', $cart) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                onclick="return confirm('Yakin ingin hapus item ini?')"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="card card-custom">
                    <div class="card-header">
                        <h5 class="mb-0">Ringkasan Belanja</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Item</span>
                            <span>{{ $carts->sum('amount') }} item</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total</strong>
                            <strong class="text-primary">Rp {{ number_format($totalPrice, 0, ',', '.') }}</strong>
                        </div>
                        
                        <form action="{{ route('checkout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 btn-lg">
                                <i class="fas fa-credit-card me-2"></i>Checkout
                            </button>
                        </form>
                        
                        <a href="{{ route('index_product') }}" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-arrow-left me-2"></i>Lanjut Belanja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
            <h4>Keranjang Masih Kosong</h4>
            <p class="text-muted mb-4">Yuk mulai belanja dan tambahkan produk ke keranjang!</p>
            <a href="{{ route('index_product') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-bag me-2"></i>Mulai Belanja
            </a>
        </div>
    @endif
</div>
@endsection