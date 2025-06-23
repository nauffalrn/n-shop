@extends('layouts.app')

@section('title', 'Kelola Variant - ' . $product->name)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-palette me-2"></i>Varian Produk: {{ $product->name }}
                </h2>
                <div class="btn-group">
                    <a href="{{ route('admin.product.variants.create', $product) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Varian
                    </a>
                    <a href="{{ route('admin.edit_product', $product) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Produk
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($variants->count() > 0)
        <div class="card card-custom">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Ukuran</th>
                                <th>Warna</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($variants as $variant)
                                <tr>
                                    <td>{{ $variant->id }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $variant->size }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $variant->color }}</span>
                                    </td>
                                    <td>
                                        <strong>Rp {{ number_format($variant->price, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>{{ $variant->stock }}</td>
                                    <td>
                                        @if($variant->stock > 0)
                                            <span class="badge bg-success">Tersedia</span>
                                        @else
                                            <span class="badge bg-danger">Habis</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.product.variants.edit', [$product, $variant]) }}" 
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </a>
                                            
                                            <form action="{{ route('admin.product.variants.destroy', [$product, $variant]) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Yakin ingin hapus varian ini?')">
                                                    <i class="fas fa-trash me-1"></i>Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-boxes fa-4x text-muted mb-4"></i>
            <h4>Belum Ada Variant</h4>
            <p class="text-muted mb-4">Tambahkan variant untuk produk {{ $product->name }}</p>
            <a href="{{ route('admin.product.variants.create', $product) }}" class="btn btn-primary btn-lg">
                <i class="fas fa-plus me-2"></i>Tambah Variant Pertama
            </a>
        </div>
    @endif
</div>
@endsection