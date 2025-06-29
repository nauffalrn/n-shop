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

                                        <!-- Di dalam loop item keranjang, tambahkan informasi berat -->
                                        <div class="product-meta">
                                            <small class="d-block text-muted">
                                                <i class="fas fa-weight me-1"></i>Berat: {{ number_format($cart->product->weight, 3) }} kg/item
                                            </small>
                                        </div>
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
                                                </form>
                                            </div>
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
                        <!-- Subtotal -->
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal ({{ $carts->count() }} item)</span>
                            <span>Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>

                        <!-- Diskon jika ada -->
                        @if(session('promo_id'))
                        <div class="d-flex justify-content-between mb-3 text-success">
                            <span>
                                <i class="fas fa-tag me-1"></i>Diskon 
                                <span class="badge bg-success">{{ $promo->code }}</span>
                            </span>
                            <span>- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        <!-- Ongkos Kirim (Estimasi) -->
                        <div class="d-flex justify-content-between mb-3">
                            <span>Estimasi Ongkos Kirim</span>
                            <div>
                                @if(Auth::user()->addresses()->exists())
                                    @php
                                        $address = Auth::user()->addresses()->first();
                                        $location = '';
                                        if($address->city) {
                                            $location = 'Kota ' . $address->city;
                                        } elseif($address->district) {
                                            $location = 'Kabupaten ' . $address->district;
                                        }
                                        
                                        // Hitung estimasi ongkir berdasarkan berat total
                                        $totalWeight = $carts->sum(function($cart) {
                                            return ($cart->product->weight ?? 0.1) * $cart->amount;
                                        });
                                        $shippingService = new \App\Services\ShippingService();
                                        $shippingEstimate = $shippingService->calculate(
                                            $address->province, 
                                            $address->country, 
                                            $totalWeight
                                        );
                                    @endphp
                                    <span class="text-dark">Rp {{ number_format($shippingEstimate, 0, ',', '.') }}</span>
                                    <small class="d-block text-muted">{{ $location }} ({{ number_format($totalWeight, 2) }} kg)</small>
                                @else
                                    <span class="text-primary" data-bs-toggle="tooltip" title="Tambahkan alamat pengiriman terlebih dahulu">
                                        <i class="fas fa-map-marker-alt me-1"></i>Tambahkan alamat
                                    </span>
                                @endif
                            </div>
                        </div>

                        <hr>

                        <!-- Total -->
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Total</strong>
                            <div class="text-end">
                                @php
                                    $shippingCost = $shippingCost ?? 0;
                                    $finalTotal = $totalPrice - ($discount ?? 0) + $shippingCost;
                                @endphp
                                
                                @if(session('promo_id'))
                                    <span class="text-decoration-line-through text-muted">
                                        Rp {{ number_format($totalPrice + $shippingCost, 0, ',', '.') }}
                                    </span><br>
                                    <strong class="text-primary fs-5">
                                        Rp {{ number_format($finalTotal, 0, ',', '.') }}
                                    </strong>
                                @else
                                    <strong class="text-primary fs-5">
                                        Rp {{ number_format($finalTotal, 0, ',', '.') }}
                                    </strong>
                                @endif
                            </div>
                        </div>

                        <!-- Checkout Button -->
                        <form action="{{ route('select.shipping') }}" method="GET">
                            <button type="submit" class="btn btn-primary w-100 btn-lg">
                                <i class="fas fa-truck me-2"></i>Lanjut ke Pengiriman
                            </button>
                        </form>

                        <!-- Promo Code Section -->
                        <div class="mt-3 mb-3">
                            <h6 class="mb-2"><i class="fas fa-tag me-2"></i>Kode Promo</h6>
                            
                            @if(session('promo_id'))
                                <div class="d-flex">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light" value="{{ $promo->code }}" disabled>
                                        <form action="{{ url('/remove-promo') }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger" type="submit">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <div class="small text-success mt-2">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ $promo->description }} - {{ $promo->getDiscountDescription() }}:
                                    <strong>Rp {{ number_format($discount, 0, ',', '.') }}</strong>
                                </div>
                            @else
                                <form action="{{ url('/apply-promo') }}" method="POST">
                                    @csrf
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="promo_code" placeholder="Masukkan kode promo" required>
                                        <button class="btn btn-outline-primary" type="submit">Terapkan</button>
                                    </div>
                                </form>
                            @endif
                            
                            @error('promo_code')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
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