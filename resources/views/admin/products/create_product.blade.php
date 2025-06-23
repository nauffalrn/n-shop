@extends('layouts.app')

@section('title', 'Tambah Produk - N-Shop Admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-plus me-2"></i>Tambah Produk
                </h2>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-body">
                    <form action="{{ route('admin.store_product') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Basic Info -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold">Nama Produk</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label fw-semibold">Kategori</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" 
                                            id="category_id" name="category_id" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="price" class="form-label fw-semibold">Harga</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                               id="price" name="price" value="{{ old('price') }}" required>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="stock" class="form-label fw-semibold">Stok</label>
                                    <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                           id="stock" name="stock" value="{{ old('stock') }}" required>
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="weight" class="form-label fw-semibold">Berat (gram)</label>
                                    <input type="number" class="form-control @error('weight') is-invalid @enderror" 
                                           id="weight" name="weight" value="{{ old('weight') }}">
                                    @error('weight')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- DRAG & DROP IMAGE UPLOAD -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Gambar Produk</label>
                            
                            <!-- Upload Area -->
                            <div id="dropZone" class="drop-zone">
                                <i class="fas fa-cloud-upload-alt fa-3x drop-zone-icon"></i>
                                <h5 class="drop-zone-title">Drag & Drop Gambar Disini</h5>
                                <p class="drop-zone-subtitle">atau klik untuk memilih file</p>
                                <input type="file" id="imageInput" name="images[]" multiple accept="image/*" class="d-none">
                                <button type="button" class="btn drop-zone-button" onclick="document.getElementById('imageInput').click()">
                                    <i class="fas fa-plus me-2"></i>Pilih Gambar
                                </button>
                            </div>

                            <!-- Preview Area -->
                            <div id="imagePreview" class="preview-section" style="display: none;">
                                <h6 class="preview-title">Preview Gambar</h6>
                                <p class="text-muted mb-3">
                                    <i class="fas fa-hand-rock me-1"></i>
                                    Drag gambar untuk mengurutkan. Gambar pertama akan menjadi gambar utama.
                                </p>
                                <div id="sortableImages" class="sortable-container">
                                    <!-- Images will be inserted here -->
                                </div>
                            </div>

                            <div class="upload-info">
                                <i class="fas fa-info-circle upload-info-icon"></i>
                                <div class="upload-info-text">
                                    <strong>Format:</strong> JPG, PNG, GIF. <strong>Ukuran:</strong> Maksimal 2MB per gambar. <strong>Jumlah:</strong> Maksimal 10 gambar.
                                </div>
                            </div>
                            
                            @error('images.*')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Produk
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
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

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.css">
<link rel="stylesheet" href="{{ asset('css/drag-drop.css') }}">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script src="{{ asset('js/drag-drop-upload.js') }}"></script>
@endpush
