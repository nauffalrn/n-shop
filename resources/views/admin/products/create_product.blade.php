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
                                    <label for="category_id" class="form-label fw-semibold">Kategori Utama</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" 
                                            id="category_id" name="category_id" required>
                                        <option value="">Pilih Kategori Utama</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Kategori utama produk untuk tampilan breadcrumb dan navigasi utama</small>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="8" style="min-height: 200px;"
                                      required>{{ old('description') }}</textarea>
                            <small class="text-muted">Masukkan deskripsi produk secara detail. Tidak ada batas karakter.</small>
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
                                    <label for="weight" class="form-label fw-semibold">Berat (kg)</label>
                                    <input type="text" class="form-control @error('weight') is-invalid @enderror" 
                                           id="weight" name="weight" value="{{ old('weight') }}" required>
                                    <small class="text-muted">Gunakan titik (.) untuk desimal. Misal: 0.181</small>
                                    @error('weight')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Ganti dari kategori utama + tambahan menjadi hanya multi-kategori -->
                        <div class="mb-3">
                            <label for="categories" class="form-label fw-semibold">Kategori Produk</label>
                            <select class="form-select select2 @error('categories') is-invalid @enderror" 
                                    id="categories" name="categories[]" multiple required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                        {{ (is_array(old('categories')) && in_array($category->id, old('categories'))) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Pilih satu atau lebih kategori. Tekan Ctrl+klik untuk memilih beberapa.</small>
                            @error('categories')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                    <strong>Format:</strong> JPG, PNG, GIF. <strong>Ukuran:</strong> Maksimal 2MB per gambar. <strong>Jumlah:</strong> Maksimal 5 gambar.
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        border-color: #ced4da;
        border-radius: 0.25rem;
        min-height: 38px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #133e87;
        color: white;
        border: none;
        padding: 2px 8px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff;
        margin-right: 5px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Sortable
    const sortableContainer = document.getElementById('sortableImages');
    if (sortableContainer) {
        new Sortable(sortableContainer, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function() {
                updatePreviewOrder();
            }
        });
    }

    // Function to update order numbers after drag & drop
    function updatePreviewOrder() {
        const items = document.querySelectorAll('#sortableImages .image-preview-item');
        
        items.forEach((item, index) => {
            // Update order indicator
            const orderIndicator = item.querySelector('.order-indicator');
            if (orderIndicator) {
                orderIndicator.textContent = index + 1;
            }
            
            // Update main badge
            let mainBadge = item.querySelector('.main-badge');
            if (index === 0) {
                item.style.border = '2px solid #27ae60';
                if (!mainBadge) {
                    mainBadge = document.createElement('div');
                    mainBadge.className = 'main-badge';
                    mainBadge.textContent = 'UTAMA';
                    const positionDiv = item.querySelector('.position-relative');
                    if (positionDiv) {
                        positionDiv.appendChild(mainBadge);
                    }
                }
            } else {
                item.style.border = '2px solid #dee2e6';
                if (mainBadge) {
                    mainBadge.remove();
                }
            }
            
            // Update data-index for delete button
            item.setAttribute('data-index', index);
            const removeBtn = item.querySelector('.remove-image');
            if (removeBtn) {
                removeBtn.setAttribute('data-index', index);
            }
        });
    }

    // Override the existing preview rendering to include proper ordering
    document.getElementById('imageInput').addEventListener('change', function() {
        const files = this.files;
        const preview = document.getElementById('imagePreview');
        const sortableContainer = document.getElementById('sortableImages');
        
        if (files.length > 0) {
            preview.style.display = 'block';
            sortableContainer.innerHTML = '';
            
            Array.from(files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'image-preview-item';
                    div.dataset.index = index;
                    
                    div.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" class="image-preview-img" alt="Preview ${index + 1}">
                            <div class="drag-handle">
                                <i class="fas fa-arrows-alt"></i>
                            </div>
                            <div class="order-indicator">${index + 1}</div>
                            ${index === 0 ? '<div class="main-badge">UTAMA</div>' : ''}
                        </div>
                        <button type="button" class="remove-image" data-index="${index}" title="Hapus gambar">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    
                    sortableContainer.appendChild(div);
                    
                    // Add event listener to remove button
                    div.querySelector('.remove-image').addEventListener('click', function() {
                        const index = this.getAttribute('data-index');
                        removeImage(index);
                    });
                }
                reader.readAsDataURL(file);
            });
        } else {
            preview.style.display = 'none';
        }
    });

    // Function to remove image from preview
    function removeImage(index) {
        const sortableContainer = document.getElementById('sortableImages');
        const items = sortableContainer.querySelectorAll('.image-preview-item');
        
        if (items[index]) {
            items[index].remove();
            
            // Update file input
            const dataTransfer = new DataTransfer();
            const fileInputs = document.getElementById('imageInput').files;
            
            for (let i = 0; i < fileInputs.length; i++) {
                if (i != index) {
                    dataTransfer.items.add(fileInputs[i]);
                }
            }
            
            document.getElementById('imageInput').files = dataTransfer.files;
            
            // Update order after removal
            updatePreviewOrder();
        }
    }

    // Initialize Select2 for multiple select
    $('#categories').select2({
        placeholder: "Pilih kategori tambahan",
        allowClear: true
    });
});
</script>
<script>
    $(document).ready(function() {
        // Inisialisasi Select2 untuk multi kategori
        $('#categories').select2({
            placeholder: 'Pilih satu atau beberapa kategori...',
            allowClear: true
        });
        
        // Pastikan kategori utama juga dipilih di multi-select
        $('#category_id').on('change', function() {
            const categoryId = $(this).val();
            if (categoryId) {
                // Cek apakah option sudah dipilih
                if(!$("#categories").find("option[value='" + categoryId + "']:selected").length) {
                    // Tambahkan ke selection
                    $("#categories").find("option[value='" + categoryId + "']").prop('selected', true);
                    $("#categories").trigger('change');
                }
            }
        });
    });
</script>
@endpush
