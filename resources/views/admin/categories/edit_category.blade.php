@extends('layouts.app')

@section('title', 'Edit Kategori - N-Shop Admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-edit me-2"></i>Edit Kategori
                </h2>
                <a href="{{ route('admin.category.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-custom">
                <div class="card-body">
                    <form action="{{ route('admin.category.update', $category) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nama Kategori</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $category->name) }}" 
                                   placeholder="Masukkan nama kategori" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Informasi Kategori</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Dibuat:</small><br>
                                            <strong>{{ $category->created_at->format('d M Y, H:i') }}</strong>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Jumlah Produk:</small><br>
                                            <strong>{{ $category->products()->count() }} produk</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Kategori
                            </button>
                            <a href="{{ route('admin.category.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection