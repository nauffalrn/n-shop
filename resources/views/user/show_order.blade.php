@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->id . ' - N-Shop')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-receipt me-2"></i>Detail Pesanan #{{ $order->id }}</h2>
                <a href="{{ route('index_order') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Pesanan
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Order Info -->
        <div class="col-lg-8">
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Order ID:</strong></td>
                                    <td>#{{ $order->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Pesanan:</strong></td>
                                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status Pembayaran:</strong></td>
                                    <td>
                                        @if($order->is_paid)
                                            <span class="badge bg-success">Lunas</span>
                                        @else
                                            @if($order->payment_receipt)
                                                <span class="badge bg-warning">Menunggu Konfirmasi</span>
                                            @else
                                                <span class="badge bg-danger">Belum Bayar</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                @if(auth()->user()->is_admin)
                                    <tr>
                                        <td><strong>Customer:</strong></td>
                                        <td>{{ $order->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $order->user->email }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td><strong>Total Item:</strong></td>
                                    <td>{{ $order->transactions->sum('umount') }} produk</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">Item Pesanan</h5>
                </div>
                <div class="card-body p-0">
                    @php $totalAmount = 0; @endphp
                    @foreach($order->transactions as $transaction)
                        @php $totalAmount += $transaction->product->price * $transaction->umount; @endphp
                        <div class="p-4 border-bottom">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    @if($transaction->product->getMainImage())
                                        <img src="{{ Storage::url($transaction->product->getMainImage()) }}" 
                                             class="img-fluid rounded" alt="{{ $transaction->product->name }}"
                                             style="height: 80px; width: 80px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="height: 80px; width: 80px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-1">{{ $transaction->product->name }}</h6>
                                    @if($transaction->product->category)
                                        <small class="text-muted">{{ $transaction->product->category->name }}</small>
                                    @endif
                                    <div class="mt-1">
                                        <a href="{{ route('show_product', $transaction->product) }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>Lihat Produk
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center">
                                    <div class="fw-bold">{{ $transaction->umount }}</div>
                                    <small class="text-muted">Qty</small>
                                </div>
                                <div class="col-md-2 text-end">
                                    <div class="fw-bold text-primary">
                                        Rp {{ number_format($transaction->product->price * $transaction->umount, 0, ',', '.') }}
                                    </div>
                                    <small class="text-muted">
                                        @Rp {{ number_format($transaction->product->price, 0, ',', '.') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="col-lg-4">
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Ringkasan Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Subtotal:</span>
                        <span>Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Ongkos Kirim:</span>
                        <span class="text-success">Gratis</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <strong>Total Pembayaran:</strong>
                        <strong class="text-primary fs-5">Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong>
                    </div>

                    <!-- Payment Status & Actions -->
                    @if($order->is_paid)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Pembayaran Berhasil!</strong><br>
                            Terima kasih atas pesanan Anda.
                        </div>
                    @else
                        @if($order->payment_receipt)
                            <div class="alert alert-warning">
                                <i class="fas fa-clock me-2"></i>
                                <strong>Menunggu Konfirmasi</strong><br>
                                Bukti pembayaran sudah diupload. Menunggu konfirmasi admin.
                            </div>
                            
                            <!-- Show uploaded receipt -->
                            <div class="mb-3">
                                <h6>Bukti Pembayaran:</h6>
                                <img src="{{ Storage::url($order->payment_receipt) }}" 
                                     class="img-fluid rounded" alt="Bukti Pembayaran">
                            </div>

                            @if(auth()->user()->is_admin)
                                <form action="{{ route('confirm_payment', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100" 
                                            onclick="return confirm('Konfirmasi pembayaran ini?')">
                                        <i class="fas fa-check me-2"></i>Konfirmasi Pembayaran
                                    </button>
                                </form>
                            @endif
                        @else
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Menunggu Pembayaran</strong><br>
                                Silakan upload bukti pembayaran Anda.
                            </div>

                            <!-- Upload Payment Receipt Form -->
                            @if(!auth()->user()->is_admin)
                                <form action="{{ route('submit_payment_receipt', $order) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="payment_receipt" class="form-label">
                                            <i class="fas fa-upload me-2"></i>Upload Bukti Pembayaran
                                        </label>
                                        <input type="file" class="form-control" id="payment_receipt" 
                                               name="payment_receipt" accept="image/*" required>
                                        <div class="form-text">
                                            Format: JPG, PNG, GIF. Maksimal 2MB.
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-upload me-2"></i>Upload Bukti Pembayaran
                                    </button>
                                </form>
                            @endif
                        @endif
                    @endif
                </div>
            </div>

            <!-- Bank Information -->
            @if(!$order->is_paid)
                <div class="card card-custom">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-university me-2"></i>Informasi Pembayaran</h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">Transfer ke rekening berikut:</p>
                        <div class="mb-3">
                            <strong>Bank BCA</strong><br>
                            <span class="text-primary">1234567890</span><br>
                            <small class="text-muted">a.n. N-Shop</small>
                        </div>
                        <div class="mb-3">
                            <strong>Bank Mandiri</strong><br>
                            <span class="text-primary">0987654321</span><br>
                            <small class="text-muted">a.n. N-Shop</small>
                        </div>
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle me-1"></i>
                            Transfer sesuai nominal yang tertera dan upload bukti pembayaran.
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Preview uploaded image
    $('#payment_receipt').on('change', function(e) {
        var file = e.target.files[0];
        if(file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                // You can add image preview here if needed
                console.log('Payment receipt selected:', file.name);
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush