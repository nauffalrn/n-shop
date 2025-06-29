@extends('layouts.app')

@section('title', 'Edit Kode Promo - N-Shop Admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-edit me-2"></i>Edit Kode Promo</h2>
                <a href="{{ route('admin.promos.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-body">
                    <form action="{{ route('admin.promos.update', $promo) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="code" class="form-label fw-semibold">Kode Promo</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                   id="code" name="code" value="{{ old('code', $promo->code) }}" 
                                   placeholder="Contoh: DISKON20" required>
                            <small class="text-muted">Kode yang akan dimasukkan user untuk mendapatkan diskon (3-15 karakter)</small>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Deskripsi</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror" 
                                   id="description" name="description" value="{{ old('description', $promo->description) }}" 
                                   placeholder="Contoh: Diskon 20% untuk semua produk" required>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label fw-semibold">Jenis Diskon</label>
                                    <select class="form-select @error('type') is-invalid @enderror" 
                                            id="type" name="type" required>
                                        <option value="percentage" {{ old('type', $promo->type) == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                                        <option value="fixed" {{ old('type', $promo->type) == 'fixed' ? 'selected' : '' }}>Nominal (Rp)</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="value" class="form-label fw-semibold">Nilai Diskon</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('value') is-invalid @enderror" 
                                               id="value" name="value" value="{{ old('value', $promo->value) }}" 
                                               placeholder="Contoh: 20" step="0.01" min="0" required>
                                        <span class="input-group-text" id="value-addon">%</span>
                                    </div>
                                    <small class="text-muted">Untuk persentase: masukkan angka 1-100</small>
                                    @error('value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_uses" class="form-label fw-semibold">Maks Penggunaan</label>
                                    <input type="number" class="form-control @error('max_uses') is-invalid @enderror" 
                                           id="max_uses" name="max_uses" value="{{ old('max_uses', $promo->max_uses ?: '') }}" 
                                           placeholder="Kosongkan untuk tidak terbatas" min="0">
                                    <small class="text-muted">Kosongkan untuk penggunaan tidak terbatas</small>
                                    @error('max_uses')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="min_purchase" class="form-label fw-semibold">Minimum Pembelian (Rp)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('min_purchase') is-invalid @enderror" 
                                               id="min_purchase" name="min_purchase" value="{{ old('min_purchase', $promo->min_purchase ?: '') }}" 
                                               placeholder="Contoh: 100000" min="0">
                                    </div>
                                    <small class="text-muted">Kosongkan jika tidak ada minimum pembelian</small>
                                    @error('min_purchase')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 max-discount-field">
                            <label for="max_discount" class="form-label fw-semibold">Maksimum Diskon (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('max_discount') is-invalid @enderror" 
                                       id="max_discount" name="max_discount" value="{{ old('max_discount', $promo->max_discount ?: '') }}" 
                                       placeholder="Contoh: 50000" min="0">
                            </div>
                            <small class="text-muted">Kosongkan jika tidak ada batas maksimum diskon</small>
                            @error('max_discount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="starts_at" class="form-label fw-semibold">Tanggal Mulai</label>
                                    <input type="datetime-local" class="form-control @error('starts_at') is-invalid @enderror" 
                                           id="starts_at" name="starts_at" 
                                           value="{{ old('starts_at', $promo->starts_at ? $promo->starts_at->format('Y-m-d\TH:i') : '') }}">
                                    <small class="text-muted">Kosongkan jika berlaku sejak sekarang</small>
                                    @error('starts_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expires_at" class="form-label fw-semibold">Tanggal Berakhir</label>
                                    <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" 
                                           id="expires_at" name="expires_at" 
                                           value="{{ old('expires_at', $promo->expires_at ? $promo->expires_at->format('Y-m-d\TH:i') : '') }}">
                                    <small class="text-muted">Kosongkan jika tidak ada batas waktu</small>
                                    @error('expires_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Info Penggunaan:</strong> Kode ini telah digunakan sebanyak {{ $promo->used_count }} kali.
                            </div>
                        </div>

                        <div class="mb-4 form-check">
                            <input class="form-check-input" type="checkbox" 
                                   id="is_active" name="is_active" value="1"
                                   {{ old('is_active', $promo->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Aktifkan promo ini
                            </label>
                        </div>

                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Kode Promo
                            </button>
                            <a href="{{ route('admin.promos.index') }}" class="btn btn-outline-secondary">
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

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle suffix pada input value berdasarkan tipe diskon
        function toggleValueAddon() {
            const type = $('#type').val();
            if (type === 'percentage') {
                $('#value-addon').text('%');
                $('.max-discount-field').show();
            } else {
                $('#value-addon').text('Rp');
                $('.max-discount-field').hide();
            }
        }
        
        // Initialize
        toggleValueAddon();
        
        // Update on change
        $('#type').on('change', toggleValueAddon);
    });
</script>
@endpush