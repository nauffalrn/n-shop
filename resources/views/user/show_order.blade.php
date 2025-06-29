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
                                    <td>{{ $order->transactions->sum('amount') }} produk</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Item Pesanan</h5>
                </div>
                <div class="card-body p-0">
                    @php $totalAmount = 0; @endphp
                    @foreach($order->transactions as $transaction)
                        @php $totalAmount += $transaction->product->price * $transaction->amount; @endphp
                        <div class="p-4 border-bottom">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    @if($transaction->product->getMainImage())
                                        <img src="{{ Storage::url($transaction->product->getMainImage()) }}" 
                                             class="img-fluid rounded" alt="{{ $transaction->product->name }}">
                                    @else
                                        <div class="bg-light rounded d-flex justify-content-center align-items-center" 
                                             style="height: 80px">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6>{{ $transaction->product->name }}</h6>
                                    <div class="text-muted small">
                                        @if($transaction->product->weight)
                                            <span class="me-2">
                                                <i class="fas fa-weight me-1"></i>{{ number_format($transaction->product->weight, 3) }} kg/item
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-2">
                                        <a href="{{ route('show_product', $transaction->product) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>Lihat Produk
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center">
                                    <div class="fw-bold">{{ $transaction->amount }}</div>
                                    <small class="text-muted">Qty</small>
                                </div>
                                <div class="col-md-2 text-end">
                                    <div class="fw-bold text-primary">
                                        Rp {{ number_format($transaction->product->price * $transaction->amount, 0, ',', '.') }}
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

            <!-- Informasi Pengiriman - dipindahkan ke sini -->
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Informasi Pengiriman</h5>
                </div>
                <div class="card-body">
                    @if($order->shipping_address)
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>{{ $order->shipping_address->name }}</strong></p>
                                <p class="mb-1">{{ $order->shipping_address->phone }}</p>
                                <p class="mb-0">
                                    {{ $order->shipping_address->address }}, 
                                    @if($order->shipping_address->city || $order->shipping_address->district)
                                        {{ $order->shipping_address->city ?: ('Kabupaten ' . $order->shipping_address->district) }}, 
                                    @endif
                                    {{ $order->shipping_address->province }}, 
                                    {{ $order->shipping_address->postal_code }}, 
                                    {{ $order->shipping_address->country }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                @php
                                    $totalWeight = $order->transactions->sum(function($transaction) {
                                        return ($transaction->product->weight ?? 0.1) * $transaction->amount;
                                    });
                                @endphp
                                <div class="d-flex align-items-center h-100">
                                    <div>
                                        <p class="mb-1">
                                            <strong>Biaya Pengiriman:</strong> 
                                            Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}
                                        </p>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-weight me-1"></i>Total berat: {{ number_format($totalWeight, 2) }} kg
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-muted">Informasi pengiriman tidak tersedia</p>
                    @endif
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
                        <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    @if($order->discount > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Diskon</span>
                        <span class="text-success">- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <strong>Total Pembayaran:</strong>
                        <strong class="text-primary fs-5">Rp {{ number_format($order->getTotalAfterDiscount(), 0, ',', '.') }}</strong>
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

                            @if($order->isRejected())
                                <div class="alert alert-danger mt-3">
                                    <strong><i class="fas fa-exclamation-circle me-2"></i>Pembayaran Ditolak</strong>
                                    <p class="mb-0 mt-2">Alasan: {{ $order->rejection_reason }}</p>
                                </div>
                                
                                @if(!auth()->user()->is_admin)
                                    <form action="{{ route('submit_payment_receipt', $order) }}" method="POST" enctype="multipart/form-data" class="mt-3">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="payment_receipt" class="form-label">
                                                <i class="fas fa-upload me-2"></i>Upload Bukti Pembayaran Baru
                                            </label>
                                            <input type="file" class="form-control" id="payment_receipt" 
                                                name="payment_receipt" accept="image/*" required>
                                            <div class="form-text">Format: JPG, PNG, GIF. Maksimal 2MB.</div>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-upload me-2"></i>Upload Bukti Pembayaran Baru
                                        </button>
                                    </form>
                                @endif
                            @endif

                            <!-- Ganti bagian untuk bukti pembayaran dan tombol admin -->
                            @if($order->payment_receipt && $order->isAwaitingConfirmation() && auth()->user()->is_admin)
                                <div class="d-flex gap-2 mt-3">
                                    <!-- Ganti tombol konfirmasi untuk menggunakan modal -->
                                    <button type="button" class="btn btn-success flex-grow-1" data-bs-toggle="modal" data-bs-target="#confirmPaymentModal">
                                        <i class="fas fa-check me-2"></i>Konfirmasi Pembayaran
                                    </button>
                                    
                                    <button type="button" class="btn btn-danger flex-grow-1" data-bs-toggle="modal" data-bs-target="#rejectPaymentModal">
                                        <i class="fas fa-times me-2"></i>Tolak Pembayaran
                                    </button>
                                </div>
                            @endif

                            @if(auth()->user()->is_admin)
                                <form id="rejectForm" action="{{ route('admin.reject_payment', $order) }}" method="POST" style="display: none;">
                                    @csrf
                                    <input type="hidden" name="rejection_reason" id="rejectionReason">
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

    <!-- Modal Tolak Pembayaran - Letakkan di luar kondisional di akhir halaman -->
    <div class="modal fade" id="rejectPaymentModal" tabindex="-1" aria-labelledby="rejectPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectPaymentModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Tolak Pembayaran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.reject_payment', $order) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Perhatian:</strong> Menolak pembayaran akan mengharuskan customer mengupload ulang bukti pembayaran.
                        </div>
                        
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">Alasan Penolakan:</label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required
                                      placeholder="Masukkan alasan mengapa bukti pembayaran ini ditolak"></textarea>
                            <div class="form-text">
                                Alasan ini akan ditampilkan kepada customer sebagai panduan untuk perbaikan.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times me-2"></i>Tolak Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Pembayaran - tambahkan ini di dekat modal tolak pembayaran -->
    <div class="modal fade" id="confirmPaymentModal" tabindex="-1" aria-labelledby="confirmPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="confirmPaymentModalLabel">
                        <i class="fas fa-check-circle me-2"></i>Konfirmasi Pembayaran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('confirm_payment', $order) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Perhatian:</strong> Mengkonfirmasi pembayaran akan menandai pesanan ini sebagai lunas dan tidak dapat dibatalkan.
                        </div>
                        
                        <p>Pastikan bukti pembayaran sudah sesuai dengan jumlah yang harus dibayarkan:</p>
                        
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total Pembayaran:</span>
                            <span class="text-primary">Rp {{ number_format($order->getTotalAfterDiscount(), 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>Konfirmasi Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Preview uploaded image (kode yang sudah ada)
    $('#payment_receipt').on('change', function(e) {
        var file = e.target.files[0];
        if(file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                console.log('Payment receipt selected:', file.name);
            };
            reader.readAsDataURL(file);
        }
    });

    // Validasi form penolakan sebelum submit
    $('#rejectPaymentModal form').on('submit', function(e) {
        const reason = $('#rejection_reason').val().trim();
        
        if (!reason) {
            e.preventDefault();
            alert('Alasan penolakan tidak boleh kosong!');
            return false;
        }
        
        // Tambahkan konfirmasi sebelum submit
        if (!confirm('Yakin ingin menolak pembayaran ini?')) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endpush