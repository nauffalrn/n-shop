@extends('layouts.app')

@section('title', 'Alamat Saya - N-Shop')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-map-marker-alt me-2"></i>Alamat Saya</h2>
                <a href="{{ route('address.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tambah Alamat
                </a>
            </div>
        </div>
    </div>

    @if($addresses->count() > 0)
        <div class="row">
            @foreach($addresses as $address)
                <div class="col-lg-6 mb-4">
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title">
                                    <i class="fas fa-home me-2 text-primary"></i>{{ $address->name }}
                                </h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('address.edit', $address) }}">
                                                <i class="fas fa-edit me-2"></i>Edit
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('address.destroy', $address) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" 
                                                        onclick="return confirm('Yakin ingin hapus alamat ini?')">
                                                    <i class="fas fa-trash me-2"></i>Hapus
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="address-details">
                                <div class="mb-2">
                                    <i class="fas fa-phone me-2 text-muted"></i>
                                    <strong>Telepon:</strong> {{ $address->phone }}
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-map-marked-alt me-2 text-muted"></i>
                                    <strong>Alamat:</strong><br>
                                    <span class="ms-4">{{ $address->address }}</span>
                                </div>
                                <div class="row">
                                    @if($address->city)
                                    <div class="col-6">
                                        <i class="fas fa-city me-2 text-muted"></i>
                                        <strong>Kota:</strong> {{ $address->city }}
                                    </div>
                                    @endif
                                    
                                    @if($address->district)
                                    <div class="col-6">
                                        <i class="fas fa-building me-2 text-muted"></i>
                                        <strong>Kabupaten:</strong> {{ $address->district }}
                                    </div>
                                    @endif
                                    
                                    <div class="col-6">
                                        <i class="fas fa-map me-2 text-muted"></i>
                                        <strong>Provinsi:</strong> {{ $address->province }}
                                    </div>
                                    
                                    <div class="col-6">
                                        <i class="fas fa-mail-bulk me-2 text-muted"></i>
                                        <strong>Kode Pos:</strong> {{ $address->postal_code }}
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <i class="fas fa-flag me-2 text-muted"></i>
                                    <strong>Negara:</strong> {{ $address->country }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-map-marker-alt fa-4x text-muted mb-4"></i>
            <h4>Belum Ada Alamat</h4>
            <p class="text-muted mb-4">Tambahkan alamat untuk memudahkan pengiriman</p>
            <a href="{{ route('address.create') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-plus me-2"></i>Tambah Alamat Pertama
            </a>
        </div>
    @endif
</div>
@endsection