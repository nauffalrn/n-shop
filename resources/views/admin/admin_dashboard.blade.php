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
        <!-- Ganti col-lg-3 menjadi col untuk pembagian lebar yang sama -->
        <div class="col mb-3">
            <div class="card stats-card h-100" style="background-color: #0d6efd;">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 style="color: #000000 !important; margin-bottom: 0.2rem;">{{ $stats['total_products'] }}</h5>
                            <p class="mb-0 small" style="color: #000000 !important;">Total Produk</p>
                        </div>
                        <i class="fas fa-box fa-lg text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col mb-3">
            <div class="card stats-card h-100" style="background-color: #27ae60;">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 style="color: #000000 !important; margin-bottom: 0.2rem;">{{ $stats['total_orders'] }}</h5>
                            <p class="mb-0 small" style="color: #000000 !important;">Total Pesanan</p>
                        </div>
                        <i class="fas fa-shopping-cart fa-lg text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col mb-3">
            <div class="card stats-card h-100" style="background-color: #f39c12;">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 style="color: #000000 !important; margin-bottom: 0.2rem;">{{ $stats['pending_orders'] }}</h5>
                            <p class="mb-0 small" style="color: #000000 !important;">Menunggu</p>
                        </div>
                        <i class="fas fa-clock fa-lg text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col mb-3">
            <div class="card stats-card h-100" style="background-color: #17a2b8;">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 style="color: #000000 !important; margin-bottom: 0.2rem;">{{ $stats['total_users'] }}</h5>
                            <p class="mb-0 small" style="color: #000000 !important;">Total User</p>
                        </div>
                        <i class="fas fa-users fa-lg text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col mb-3">
            <div class="card stats-card h-100" style="background-color: #20c997;">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 style="color: #000000 !important; margin-bottom: 0.2rem;">{{ \App\Models\Promo::where('is_active', true)->count() }}</h5>
                            <p class="mb-0 small" style="color: #000000 !important;">Promo Aktif</p>
                        </div>
                        <i class="fas fa-ticket-alt fa-lg text-white"></i>
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
                <a href="{{ route('admin.promos.create') }}" class="btn btn-info">
                    <i class="fas fa-plus me-2"></i>Tambah Promo
                </a>
                <a href="{{ route('admin.orders') }}" class="btn btn-warning">
                    <i class="fas fa-list me-2"></i>Lihat Semua Pesanan
                </a>
                <a href="{{ route('admin.users') }}" class="btn btn-secondary">
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
                <div class="card-header bg-warning-subtle bg-opacity-10">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-circle text-danger me-2"></i>Stok Menipis
                        </h5>
                        <span class="badge bg-danger">{{ $lowStockProducts->count() }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($lowStockProducts->count() > 0)
                        <div class="low-stock-container" style="max-height: 300px; overflow-y: auto;">
                            @foreach($lowStockProducts as $product)
                                <a href="{{ route('show_product', $product) }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center p-3 border-bottom position-relative hover-bg-light">
                                        <div class="flex-shrink-0 me-3">
                                            @if($product->getMainImage())
                                                <img src="{{ Storage::url($product->getMainImage()) }}" class="rounded" 
                                                     alt="{{ $product->name }}" width="40" height="40" style="object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-box text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 text-truncate" style="max-width: 200px;" title="{{ $product->name }}">
                                                {{ $product->name }}
                                            </h6>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-danger me-2">Stok: {{ $product->stock }}</span>
                                                <small class="text-muted">
                                                    {{ $product->category ? $product->category->name : 'Tanpa kategori' }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="ms-2">
                                            <i class="fas fa-chevron-right text-muted"></i>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                            <p class="text-muted mb-0">Semua produk stok aman</p>
                        </div>
                    @endif
                </div>
                @if($lowStockProducts->count() > 0)
                    <div class="card-footer bg-light">
                        <a href="{{ route('index_product') }}" class="text-decoration-none">
                            <div class="d-flex justify-content-center align-items-center">
                                <small class="text-muted">Lihat semua produk</small>
                                <i class="fas fa-arrow-right ms-2"></i>
                            </div>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection