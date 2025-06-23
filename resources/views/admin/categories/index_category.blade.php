@extends('layouts.app')

@section('title', 'Kelola Kategori - N-Shop Admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-tags me-2"></i>Kelola Kategori
                </h2>
                <a href="{{ route('admin.category.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tambah Kategori
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-body">
                    @if($categories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Kategori</th>
                                        <th>Jumlah Produk</th>
                                        <th>Tanggal Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                        <tr>
                                            <td>{{ $category->id }}</td>
                                            <td>
                                                <strong>{{ $category->name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $category->products_count }} produk</span>
                                            </td>
                                            <td>{{ $category->created_at->format('d M Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.category.edit', $category) }}" 
                                                       class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-edit me-1"></i>Edit
                                                    </a>
                                                    
                                                    @if($category->products_count == 0)
                                                        <form action="{{ route('admin.category.destroy', $category) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                    onclick="return confirm('Yakin ingin hapus kategori ini?')">
                                                                <i class="fas fa-trash me-1"></i>Hapus
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button class="btn btn-sm btn-outline-secondary" disabled
                                                                title="Tidak dapat dihapus karena masih ada produk">
                                                            <i class="fas fa-ban me-1"></i>Tidak Dapat Dihapus
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                            <h4>Belum Ada Kategori</h4>
                            <p class="text-muted mb-4">Mulai dengan menambahkan kategori pertama</p>
                            <a href="{{ route('admin.category.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Tambah Kategori Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection