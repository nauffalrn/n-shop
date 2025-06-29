@extends('layouts.app')

@section('title', 'Pesanan Saya - N-Shop')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-box me-2"></i>Pesanan Saya
            </h2>
        </div>
    </div>

    @if($orders->count() > 0)
        <div class="row">
            @foreach($orders as $order)
                <div class="col-12 mb-4">
                    <div class="card card-custom">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">
                                    <i class="fas fa-receipt me-2"></i>Order #{{ $order->id }}
                                </h6>
                                <small class="text-muted">{{ $order->created_at->format('d M Y, H:i') }}</small>
                            </div>
                            <div>
                                @if($order->is_paid)
                                    <span class="badge bg-success">Sudah Dibayar</span>
                                @elseif($order->payment_receipt)
                                    <span class="badge bg-warning">Menunggu Konfirmasi</span>
                                @else
                                    <span class="badge bg-danger">Belum Dibayar</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6>Produk yang Dipesan:</h6>
                                    @php $totalOrderAmount = 0; @endphp
                                    @foreach($order->transactions as $transaction)
                                        @php 
                                            $itemTotal = $transaction->product->price * $transaction->amount;
                                            $totalOrderAmount += $itemTotal; 
                                        @endphp
                                        <div class="d-flex align-items-center mb-2">
                                            @if($transaction->product->getMainImage())
                                                <img src="{{ Storage::url($transaction->product->getMainImage()) }}" 
                                                     class="img-thumbnail me-3" width="50" height="50" 
                                                     alt="{{ $transaction->product->name }}">
                                            @else
                                                <div class="img-thumbnail me-3 d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ $transaction->product->name }}</div>
                                                <small class="text-muted">
                                                    {{ $transaction->amount }} x Rp {{ number_format($transaction->product->price, 0, ',', '.') }}
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body p-3">
                                            <div class="mb-2">
                                                <strong class="d-block">Total Pembayaran:</strong>
                                                <span class="text-primary fs-5">
                                                    Rp {{ number_format($order->getTotalAfterDiscount(), 0, ',', '.') }}
                                                </span>
                                            </div>
                                            <a href="{{ route('show_order', $order) }}" class="btn btn-primary btn-sm w-100">
                                                <i class="fas fa-eye me-2"></i>Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-4x text-muted mb-4"></i>
            <h4>Belum Ada Pesanan</h4>
            <p class="text-muted mb-4">Anda belum pernah melakukan pemesanan</p>
            <a href="{{ route('index_product') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-bag me-2"></i>Mulai Belanja
            </a>
        </div>
    @endif
</div>
@endsection