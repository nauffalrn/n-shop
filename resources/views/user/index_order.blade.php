@extends('layouts.app')

@section('title', 'Pesanan Saya - N-Shop')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-receipt me-2"></i>
                    {{ auth()->user()->is_admin ? 'Semua Pesanan' : 'Pesanan Saya' }}
                </h2>
                <a href="{{ route('index_product') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali Belanja
                </a>
            </div>
        </div>
    </div>

    @if($orders->count() > 0)
        <div class="row">
            <div class="col-12">
                @foreach($orders as $order)
                    <div class="card card-custom mb-4">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-0">
                                        <i class="fas fa-shopping-bag me-2"></i>
                                        Order #{{ $order->id }}
                                    </h6>
                                    <small class="text-muted">
                                        {{ $order->created_at->format('d M Y H:i') }}
                                    </small>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    @if($order->is_paid)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Lunas
                                        </span>
                                    @else
                                        @if($order->payment_receipt)
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>Menunggu Konfirmasi
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i>Belum Bayar
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Customer Info (Admin only) -->
                            @if(auth()->user()->is_admin)
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Customer:</strong> {{ $order->user->name }}<br>
                                        <strong>Email:</strong> {{ $order->user->email }}
                                    </div>
                                </div>
                                <hr>
                            @endif

                            <!-- Order Items -->
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="mb-3">Item Pesanan:</h6>
                                    @php $totalAmount = 0; @endphp
                                    @foreach($order->transactions as $transaction)
                                        @php $totalAmount += $transaction->product->price * $transaction->umount; @endphp
                                        <div class="d-flex align-items-center mb-3">
                                            @if($transaction->product->getMainImage())
                                                <img src="{{ Storage::url($transaction->product->getMainImage()) }}" 
                                                     class="rounded me-3" width="60" height="60" 
                                                     style="object-fit: cover;" alt="{{ $transaction->product->name }}">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 60px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $transaction->product->name }}</h6>
                                                <small class="text-muted">
                                                    {{ $transaction->umount }} x Rp {{ number_format($transaction->product->price, 0, ',', '.') }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <strong>Rp {{ number_format($transaction->product->price * $transaction->umount, 0, ',', '.') }}</strong>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="mb-3">Ringkasan Pesanan</h6>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Subtotal:</span>
                                                <span>Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Ongkir:</span>
                                                <span class="text-muted">Gratis</span>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between">
                                                <strong>Total:</strong>
                                                <strong class="text-primary">Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('show_order', $order) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-2"></i>Detail Pesanan
                                    </a>
                                </div>
                                <div>
                                    @if(!$order->is_paid && !$order->payment_receipt)
                                        <!-- Belum upload bukti pembayaran -->
                                        <span class="text-muted small">Silakan upload bukti pembayaran</span>
                                    @elseif(!$order->is_paid && $order->payment_receipt)
                                        <!-- Sudah upload, menunggu konfirmasi admin -->
                                        @if(auth()->user()->is_admin)
                                            <form action="{{ route('admin.confirm_payment', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" 
                                                        onclick="return confirm('Konfirmasi pembayaran?')">
                                                    <i class="fas fa-check me-2"></i>Konfirmasi Pembayaran
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-warning small">Menunggu konfirmasi admin</span>
                                        @endif
                                    @else
                                        <!-- Sudah lunas -->
                                        <span class="text-success small">
                                            <i class="fas fa-check-circle me-1"></i>Pembayaran berhasil dikonfirmasi
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- Empty Orders -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-receipt fa-4x text-muted mb-4"></i>
                    <h4>Belum Ada Pesanan</h4>
                    <p class="text-muted mb-4">
                        {{ auth()->user()->is_admin ? 'Belum ada pesanan dari customer.' : 'Anda belum memiliki pesanan. Yuk mulai belanja!' }}
                    </p>
                    @if(!auth()->user()->is_admin)
                        <a href="{{ route('index_product') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-shopping-bag me-2"></i>Mulai Belanja
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection