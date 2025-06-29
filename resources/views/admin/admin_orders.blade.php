@extends('layouts.app')

@section('title', 'Kelola Pesanan - N-Shop Admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-shopping-cart me-2"></i>Kelola Pesanan
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-body">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>#{{ $order->id }}</td>
                                            <td>{{ $order->user->name }}</td>
                                            <td>
                                                Rp {{ number_format($order->getTotalAfterDiscount(), 0, ',', '.') }}
                                            </td>
                                            <td>
                                                @if($order->isConfirmed())
                                                    <span class="badge bg-success">Dibayar</span>
                                                @elseif($order->isAwaitingConfirmation())
                                                    <span class="badge bg-warning">Menunggu Konfirmasi</span>
                                                @elseif($order->isRejected())
                                                    <span class="badge bg-danger">Ditolak</span>
                                                    <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" 
                                                       title="Alasan: {{ $order->rejection_reason }}"></i>
                                                @else
                                                    <span class="badge bg-secondary">Belum Bayar</span>
                                                @endif
                                            </td>
                                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if($order->isAwaitingConfirmation())
                                                        <a href="{{ route('show_order', $order) }}" 
                                                           class="btn btn-sm btn-warning">
                                                            <i class="fas fa-check-circle me-1"></i>Periksa
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h4>Belum Ada Pesanan</h4>
                            <p class="text-muted">Pesanan akan muncul di sini ketika user mulai berbelanja</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection