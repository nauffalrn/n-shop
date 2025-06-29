@extends('layouts.app')

@section('title', 'Tambah Variant - ' . $product->name)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-plus me-2"></i>Tambah Variant Baru
                    </h5>
                    <p class="mb-0 text-muted">Produk: {{ $product->name }}</p>
                </div>
                <div class="card-body">
                    <!-- Form action -->
                    <form action="{{ route('admin.product.variants.store', $product) }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="size" class="form-label fw-semibold">
                                        <i class="fas fa-ruler me-2"></i>Ukuran (opsional)
                                    </label>
                                    <input type="text" class="form-control @error('size') is-invalid @enderror" 
                                           id="size" name="size" value="{{ old('size') }}" 
                                           placeholder="S, M, L, XL">
                                    @error('size')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="color" class="form-label fw-semibold">
                                        <i class="fas fa-palette me-2"></i>Warna (opsional)
                                    </label>
                                    <input type="text" class="form-control @error('color') is-invalid @enderror" 
                                           id="color" name="color" value="{{ old('color') }}" 
                                           placeholder="Merah, Biru, Hitam">
                                    @error('color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="type" class="form-label fw-semibold">
                                        <i class="fas fa-tag me-2"></i>Tipe (opsional)
                                    </label>
                                    <input type="text" class="form-control @error('type') is-invalid @enderror" 
                                           id="type" name="type" value="{{ old('type') }}" 
                                           placeholder="Premium, Basic, Standard">
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Info:</strong> Setidaknya salah satu dari Ukuran, Warna, atau Tipe harus diisi untuk membedakan variant.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label fw-semibold">
                                        <i class="fas fa-tag me-2"></i>Harga
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                               id="price" name="price" value="{{ old('price') }}" 
                                               placeholder="0" min="0" required>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock" class="form-label fw-semibold">
                                        <i class="fas fa-boxes me-2"></i>Stok
                                    </label>
                                    <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                           id="stock" name="stock" value="{{ old('stock') }}" 
                                           placeholder="0" min="0" required>
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Variant
                            </button>
                            <!-- Back button -->
                            <a href="{{ route('admin.product.variants.index', $product) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection