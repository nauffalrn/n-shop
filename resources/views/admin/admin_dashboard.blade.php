@extends('layouts.app')

@section('title', 'Dashboard Admin - N-Shop')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin
            </h2>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['total_products'] }}</h4>
                            <p class="mb-0">Total Produk</p>
                        </div>
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['total_orders'] }}</h4>
                            <p class="mb-0">Total Pesanan</p>
                        </div>
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['pending_orders'] }}</h4>
                            <p class="mb-0">Menunggu Konfirmasi</p>
                        </div>
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['total_users'] }}</h4>
                            <p class="mb-0">Total User</p>
                        </div>
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <h4>Quick Actions</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.create_product') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tambah Produk
                </a>
                <a href="{{ route('admin.category.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Tambah Kategori
                </a>
                <a href="{{ route('admin.orders') }}" class="btn btn-warning">
                    <i class="fas fa-list me-2"></i>Lihat Semua Pesanan
                </a>
                <a href="{{ route('admin.users') }}" class="btn btn-info">
                    <i class="fas fa-users me-2"></i>Kelola User
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Pesanan Terbaru</h5>
                </div>
                <div class="card-body">
                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
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
                                                    <span class="badge bg-warning">Pending</span>
                                                @else
                                                    <span class="badge bg-danger">Belum Bayar</span>
                                                @endif
                                            </td>
                                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Belum ada pesanan</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>Stok Menipis</h5>
                </div>
                <div class="card-body">
                    @if($lowStockProducts->count() > 0)
                        @foreach($lowStockProducts as $product)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <small class="fw-bold">{{ Str::limit($product->name, 25) }}</small><br>
                                    <small class="text-danger">Stok: {{ $product->stock }}</small>
                                </div>
                                <a href="{{ route('admin.edit_product', $product) }}" class="btn btn-sm btn-outline-primary">
                                    Edit
                                </a>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">Semua produk stok aman</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection