@extends('layouts.app')

@section('title', 'Edit Produk - N-Shop Admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-edit me-2"></i>Edit Produk
                </h2>
                <a href="{{ route('show_product', $product) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Edit Produk: {{ $product->name }}</h2>
                        <div>
                            <a href="{{ route('admin.product.variants.index', $product) }}" class="btn btn-info">
                                <i class="fas fa-palette me-2"></i>Kelola Varian Produk
                            </a>
                        </div>
                    </div>
                    <form action="{{ route('admin.update_product', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        
                        <!-- Basic Info -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold">Nama Produk</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $product->name) }}" required>
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
                                            <option value="{{ $category->id }}" 
                                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                                      required>{{ old('description', $product->description) }}</textarea>
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
                                               id="price" name="price" value="{{ old('price', $product->price) }}" required>
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
                                           id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required>
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="weight" class="form-label fw-semibold">Berat (kg)</label>
                                    <input type="text" class="form-control @error('weight') is-invalid @enderror" 
                                           id="weight" name="weight" value="{{ old('weight', $product->weight) }}" required>
                                    <small class="text-muted">Gunakan titik (.) untuk desimal. Misal: 0.181</small>
                                    @error('weight')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- KATEGORI PRODUK -->
                        <div class="mb-3">
                            <label for="categories" class="form-label fw-semibold">Kategori Produk</label>
                            <select class="form-select select2 @error('categories') is-invalid @enderror" 
                                    id="categories" name="categories[]" multiple required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                        {{ (is_array(old('categories', $product->categories->pluck('id')->toArray())) && 
                                           in_array($category->id, old('categories', $product->categories->pluck('id')->toArray()))) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Pilih satu atau lebih kategori. Tekan Ctrl+klik untuk memilih beberapa.</small>
                            @error('categories')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- EXISTING IMAGES SECTION -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Gambar Produk Saat Ini</label>
                            
                            @if($product->images && count($product->images) > 0)
                                
                                <div id="existingImages" class="sortable-container">
                                    @foreach($product->images as $index => $image)
                                        <div class="existing-image-item position-relative {{ $index == 0 ? 'main-image' : '' }}">
                                            <div class="position-relative">
                                                <img src="{{ Storage::url($image) }}" class="existing-image" alt="Produk {{ $index + 1 }}">
                                                <div class="drag-handle">
                                                    <i class="fas fa-arrows-alt"></i>
                                                </div>
                                                <div class="order-indicator">{{ $index + 1 }}</div>
                                                @if($index === 0)
                                                    <div class="main-badge">UTAMA</div>
                                                @endif
                                            </div>
                                            <button type="button" class="remove-existing-image" 
                                                    onclick="window.removeExistingImage('{{ $index }}')" title="Hapus gambar">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <input type="hidden" name="existing_images[]" value="{{ $image }}">
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Produk ini belum memiliki gambar. Silakan tambahkan minimal 1 gambar.
                                </div>
                            @endif
                        </div>

                        <!-- DRAG & DROP IMAGE UPLOAD -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Tambah Gambar Baru</label>
                            
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
                            <div id="newImagePreview" class="preview-section" style="display: none;">
                                <h6 class="preview-title">Preview Gambar</h6>
                                <p class="text-muted mb-3">
                                    <i class="fas fa-hand-rock me-1"></i>
                                    Drag gambar untuk mengurutkan.
                                </p>
                                <div id="sortableNewImages" class="sortable-container">
                                    <!-- New images will be inserted here -->
                                </div>
                            </div>

                            <div class="upload-info">
                                <i class="fas fa-info-circle upload-info-icon"></i>
                                <div class="upload-info-text">
                                    <strong>Format:</strong> JPG, PNG, GIF. <strong>Ukuran:</strong> Maksimal 2MB per gambar
                                </div>
                            </div>
                            
                            @error('images.*')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('show_product', $product) }}" class="btn btn-outline-secondary">
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
    .existing-image-item {
        border: 2px solid #dee2e6;
        display: inline-block;
        margin: 10px;
        padding: 10px;
        background-color: #fff;
        border-radius: 8px;
        position: relative;
        cursor: move;
        overflow: hidden;
        width: 120px;
        height: 120px;
    }
    
    .existing-image-item.main-image {
        border: 2px solid #27ae60;
    }
    
    .existing-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 4px;
    }
    
    .main-badge {
        position: absolute;
        top: 5px;
        left: 5px;
        background-color: var(--success-color);
        color: white;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: bold;
    }
    
    .remove-existing-image {
        position: absolute !important;
        top: 5px !important;
        right: 5px !important;
        background-color: #fff !important;
        border: 2px solid #e74c3c !important;
        border-radius: 50% !important;
        width: 28px !important;
        height: 28px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        color: #e74c3c !important;
        z-index: 100 !important;
    }
    
    .remove-existing-image:hover {
        background-color: #e74c3c !important;
        color: white !important;
    }
    
    .remove-existing-image i {
        pointer-events: none !important;
    }
    
    .order-indicator {
        position: absolute;
        bottom: 5px;
        left: 5px;
        background-color: rgba(0, 0, 0, 0.6);
        color: white;
        font-size: 12px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/edit-product.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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

// Fungsi global untuk menghapus gambar
window.removeExistingImage = function(index) {
    console.log("Menghapus gambar index:", index);
    
    // Ambil container dan item
    const container = document.getElementById("existingImages");
    const items = container.querySelectorAll(".existing-image-item");
    
    // Cek apakah item dengan index tersebut ada
    if (items[index]) {
        // Hapus item dari DOM
        items[index].remove();
        
        // Update urutan setelah penghapusan
        updateExistingOrder();
        
        // Tampilkan notifikasi
        showNotification("Gambar berhasil dihapus!");
    }
};

// Fungsi untuk update urutan
function updateExistingOrder() {
    const container = document.getElementById("existingImages");
    const items = container.querySelectorAll(".existing-image-item");
    
    items.forEach((item, index) => {
        // Update order indicator
        const orderIndicator = item.querySelector(".order-indicator");
        if (orderIndicator) {
            orderIndicator.textContent = index + 1;
        }
        
        // Update main badge
        let mainBadge = item.querySelector(".main-badge");
        if (index === 0) {
            item.classList.add("main-image");
            if (!mainBadge) {
                mainBadge = document.createElement("div");
                mainBadge.className = "main-badge";
                mainBadge.textContent = "UTAMA";
                item.querySelector(".position-relative").appendChild(mainBadge);
            }
        } else {
            item.classList.remove("main-image");
            if (mainBadge) {
                mainBadge.remove();
            }
        }
        
        // Update tombol hapus
        const removeBtn = item.querySelector(".remove-existing-image");
        if (removeBtn) {
            removeBtn.setAttribute("onclick", `window.removeExistingImage(${index})`);
        }
    });
}

// Fungsi untuk menampilkan notifikasi
function showNotification(message) {
    const notification = document.createElement("div");
    notification.className = "alert alert-info alert-dismissible fade show position-fixed";
    notification.style.top = "20px";
    notification.style.right = "20px";
    notification.style.zIndex = "9999";
    notification.innerHTML = `
        <i class="fas fa-info-circle me-2"></i> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endpush
