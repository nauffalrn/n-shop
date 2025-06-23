@extends('layouts.app')

@section('title', 'Keranjang Belanja - N-Shop')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-shopping-cart me-2"></i>Keranjang Belanja</h2>
                <a href="{{ route('index_product') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Lanjut Belanja
                </a>
            </div>
        </div>
    </div>

    @if($carts->count() > 0)
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card card-custom">
                    <div class="card-header">
                        <h5 class="mb-0">Item di Keranjang ({{ $carts->count() }})</h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($carts as $cart)
                            <div class="cart-item p-4 border-bottom">
                                <div class="row align-items-center">
                                    <!-- Product Image -->
                                    <div class="col-md-2">
                                        @if($cart->product->getMainImage())
                                            <img src="{{ Storage::url($cart->product->getMainImage()) }}" 
                                                 class="img-fluid rounded" alt="{{ $cart->product->name }}"
                                                 style="height: 80px; width: 80px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="height: 80px; width: 80px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Product Info -->
                                    <div class="col-md-4">
                                        <h6 class="mb-1">
                                            <a href="{{ route('show_product', $cart->product) }}" 
                                               class="text-decoration-none">
                                                {{ $cart->product->name }}
                                            </a>
                                        </h6>
                                        @if($cart->product->category)
                                            <small class="text-muted">{{ $cart->product->category->name }}</small>
                                        @endif
                                        @if($cart->variant)
                                            <div class="mt-1">
                                                <span class="badge bg-secondary">{{ $cart->variant->getVariantName() }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Price -->
                                    <div class="col-md-2">
                                        <strong class="text-primary">
                                            Rp {{ number_format($cart->getFinalPrice(), 0, ',', '.') }}
                                        </strong>
                                    </div>

                                    <!-- Quantity -->
                                    <div class="col-md-2">
                                        <form action="{{ route('update_cart', $cart) }}" method="POST" class="quantity-form">
                                            @csrf
                                            @method('PATCH')
                                            <div class="input-group input-group-sm">
                                                <button type="button" class="btn btn-outline-secondary qty-btn" data-action="decrease">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" name="amount" class="form-control text-center qty-input" 
                                                       value="{{ $cart->amount }}" min="1" 
                                                       max="{{ $cart->variant ? $cart->variant->stock : $cart->product->stock }}">
                                                <button type="button" class="btn btn-outline-secondary qty-btn" data-action="increase">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Subtotal & Actions -->
                                    <div class="col-md-2">
                                        <div class="text-end">
                                            <div class="fw-bold text-primary mb-2">
                                                Rp {{ number_format($cart->getTotalPrice(), 0, ',', '.') }}
                                            </div>
                                            <form action="{{ route('delete_cart', $cart) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                        onclick="return confirm('Yakin ingin menghapus item ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
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
                        <h5 class="mb-0">Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal ({{ $carts->count() }} item)</span>
                            <span>Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span>Ongkos Kirim</span>
                            <span class="text-muted">Dihitung saat checkout</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Total</strong>
                            <strong class="text-primary fs-5">Rp {{ number_format($totalPrice, 0, ',', '.') }}</strong>
                        </div>

                        <!-- Checkout Button -->
                        <form action="{{ route('checkout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 btn-lg">
                                <i class="fas fa-credit-card me-2"></i>Checkout
                            </button>
                        </form>

                        <!-- Promo Code -->
                        <div class="mt-3">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Kode Promo">
                                <button class="btn btn-outline-secondary" type="button">Terapkan</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shopping Tips -->
                <div class="card card-custom mt-3">
                    <div class="card-body">
                        <h6><i class="fas fa-lightbulb me-2 text-warning"></i>Tips Belanja</h6>
                        <ul class="list-unstyled small text-muted mb-0">
                            <li><i class="fas fa-check text-success me-2"></i>Gratis ongkir min. pembelian Rp 100.000</li>
                            <li><i class="fas fa-check text-success me-2"></i>Garansi 100% uang kembali</li>
                            <li><i class="fas fa-check text-success me-2"></i>Customer service 24/7</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Cart -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                    <h4>Keranjang Anda Kosong</h4>
                    <p class="text-muted mb-4">Belum ada produk yang ditambahkan ke keranjang. Yuk mulai belanja!</p>
                    <a href="{{ route('index_product') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Mulai Belanja
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Quantity buttons
    $('.qty-btn').on('click', function() {
        var action = $(this).data('action');
        var input = $(this).closest('.input-group').find('.qty-input');
        var currentVal = parseInt(input.val());
        var max = parseInt(input.attr('max'));
        
        if(action === 'increase' && currentVal < max) {
            input.val(currentVal + 1);
        } else if(action === 'decrease' && currentVal > 1) {
            input.val(currentVal - 1);
        }
        
        // Auto submit form
        input.closest('.quantity-form').submit();
    });

    // Manual input change
    $('.qty-input').on('change', function() {
        $(this).closest('.quantity-form').submit();
    });
});
</script>
@endpush