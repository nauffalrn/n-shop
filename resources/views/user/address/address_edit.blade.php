@extends('layouts.app')

@section('title', 'Edit Alamat - N-Shop')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Alamat: {{ $address->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('address.update', $address) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold">
                                        <i class="fas fa-tag me-2"></i>Label Alamat
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $address->name) }}" 
                                           placeholder="Contoh: Rumah, Kantor, Apartemen" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label fw-semibold">
                                        <i class="fas fa-phone me-2"></i>Nomor Telepon
                                    </label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $address->phone) }}" 
                                           placeholder="08123456789" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label fw-semibold">
                                <i class="fas fa-map-marked-alt me-2"></i>Alamat Lengkap
                            </label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3" required 
                                      placeholder="Masukkan alamat lengkap dengan detail">{{ old('address', $address->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="city" class="form-label fw-semibold">
                                        <i class="fas fa-city me-2"></i>Kota
                                    </label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                           id="city" name="city" value="{{ old('city', $address->city) }}" 
                                           placeholder="Jakarta" required>
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="postal_code" class="form-label fw-semibold">
                                        <i class="fas fa-mail-bulk me-2"></i>Kode Pos
                                    </label>
                                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                           id="postal_code" name="postal_code" value="{{ old('postal_code', $address->postal_code) }}" 
                                           placeholder="12345" required>
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="country" class="form-label fw-semibold">
                                <i class="fas fa-flag me-2"></i>Negara
                            </label>
                            <select class="form-select @error('country') is-invalid @enderror" 
                                    id="country" name="country" required>
                                <option value="">Pilih Negara</option>
                                <option value="Indonesia" {{ old('country', $address->country) == 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                                <option value="Malaysia" {{ old('country', $address->country) == 'Malaysia' ? 'selected' : '' }}>Malaysia</option>
                                <option value="Singapore" {{ old('country', $address->country) == 'Singapore' ? 'selected' : '' }}>Singapore</option>
                                <option value="Thailand" {{ old('country', $address->country) == 'Thailand' ? 'selected' : '' }}>Thailand</option>
                            </select>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Alamat
                            </button>
                            <a href="{{ route('address.index') }}" class="btn btn-outline-secondary">
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