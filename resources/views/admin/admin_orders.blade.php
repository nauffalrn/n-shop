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
                                                @php
                                                    $total = $order->transactions->sum(function($t) {
                                                        return $t->product->price * $t->umount;
                                                    });
                                                @endphp
                                                Rp {{ number_format($total, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                @if($order->is_paid)
                                                    <span class="badge bg-success">Dibayar</span>
                                                @elseif($order->payment_receipt)
                                                    <span class="badge bg-warning">Menunggu Konfirmasi</span>
                                                @else
                                                    <span class="badge bg-danger">Belum Bayar</span>
                                                @endif
                                            </td>
                                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('show_order', $order) }}" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-eye me-1"></i>Detail
                                                    </a>
                                                    
                                                    @if($order->payment_receipt && !$order->is_paid)
                                                        <form action="{{ route('admin.confirm_payment', $order) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success"
                                                                    onclick="return confirm('Konfirmasi pembayaran pesanan ini?')">
                                                                <i class="fas fa-check me-1"></i>Konfirmasi
                                                            </button>
                                                        </form>
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