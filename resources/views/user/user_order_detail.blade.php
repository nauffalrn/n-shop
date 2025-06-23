@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->id . ' - N-Shop')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-receipt me-2"></i>Detail Pesanan #{{ $order->id }}
                </h2>
                <a href="{{ route('index_order') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Order Items -->
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Produk yang Dipesan</h5>
                </div>
                <div class="card-body">
                    @php $total = 0; @endphp
                    @foreach($order->transactions as $transaction)
                        @php 
                            $subtotal = $transaction->product->price * $transaction->umount;
                            $total += $subtotal;
                        @endphp
                        <div class="d-flex align-items-center border-bottom py-3">
                            @if($transaction->product->getMainImage())
                                <img src="{{ Storage::url($transaction->product->getMainImage()) }}" 
                                     class="img-thumbnail me-3" width="80" height="80" 
                                     alt="{{ $transaction->product->name }}">
                            @else
                                <div class="img-thumbnail me-3 d-flex align-items-center justify-content-center" 
                                     style="width: 80px; height: 80px;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            @endif
                            
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $transaction->product->name }}</h6>
                                <p class="text-muted mb-2">{{ Str::limit($transaction->product->description, 100) }}</p>
                                <div class="d-flex justify-content-between">
                                    <span>{{ $transaction->umount }} x Rp {{ number_format($transaction->product->price, 0, ',', '.') }}</span>
                                    <strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="text-end mt-3 pt-3 border-top">
                        <h5>Total: <span class="text-primary">Rp {{ number_format($total, 0, ',', '.') }}</span></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Order Status -->
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Status Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Status Pembayaran:</strong><br>
                        @if($order->is_paid)
                            <span class="badge bg-success">Sudah Dibayar</span>
                        @elseif($order->payment_receipt)
                            <span class="badge bg-warning">Menunggu Konfirmasi</span>
                        @else
                            <span class="badge bg-danger">Belum Dibayar</span>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <strong>Tanggal Pesanan:</strong><br>
                        {{ $order->created_at->format('d M Y, H:i') }} WIB
                    </div>
                    
                    <div class="mb-3">
                        <strong>Pemesan:</strong><br>
                        {{ $order->user->name }}<br>
                        <small class="text-muted">{{ $order->user->email }}</small>
                    </div>
                </div>
            </div>

            <!-- Payment Upload -->
            @if(!$order->is_paid)
                <div class="card card-custom">
                    <div class="card-header">
                        <h5 class="mb-0">Upload Bukti Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        @if($order->payment_receipt)
                            <div class="mb-3">
                                <strong>Bukti Pembayaran:</strong><br>
                                <img src="{{ Storage::url($order->payment_receipt) }}" 
                                     class="img-fluid rounded border" alt="Bukti Pembayaran">
                                <div class="mt-2">
                                    <span class="badge bg-warning">Menunggu Konfirmasi Admin</span>
                                </div>
                            </div>
                        @else
                            <form action="{{ route('submit_payment_receipt', $order) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="payment_receipt" class="form-label">Pilih Bukti Pembayaran</label>
                                    <input type="file" class="form-control" id="payment_receipt" 
                                           name="payment_receipt" accept="image/*" required>
                                    <small class="text-muted">Format: JPG, PNG, GIF. Max: 2MB</small>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-upload me-2"></i>Upload Bukti
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Admin Actions -->
            @if(Auth::user()->is_admin && !$order->is_paid && $order->payment_receipt)
                <div class="card card-custom mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Aksi Admin</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('confirm_payment', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" 
                                    onclick="return confirm('Konfirmasi pembayaran pesanan ini?')">
                                <i class="fas fa-check me-2"></i>Konfirmasi Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection