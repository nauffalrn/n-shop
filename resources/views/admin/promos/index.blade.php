@extends('layouts.app')

@section('title', 'Kelola Kode Promo - N-Shop Admin')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-tags me-2"></i>Kelola Kode Promo</h2>
            <a href="{{ route('admin.promos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Kode Promo
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-body">
                    @if($promos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Deskripsi</th>
                                        <th>Jenis</th>
                                        <th>Nilai</th>
                                        <th>Penggunaan</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($promos as $promo)
                                        <tr>
                                            <td><span class="badge bg-primary">{{ $promo->code }}</span></td>
                                            <td>{{ $promo->description }}</td>
                                            <td>
                                                @if($promo->type == 'percentage')
                                                    Persen ({{ $promo->value }}%)
                                                @else
                                                    Nominal (Rp{{ number_format($promo->value, 0, ',', '.') }})
                                                @endif
                                            </td>
                                            <td>
                                                @if($promo->type == 'percentage')
                                                    {{ $promo->value }}%
                                                    @if($promo->max_discount)
                                                        <br>
                                                        <small>(Maks Rp{{ number_format($promo->max_discount, 0, ',', '.') }})</small>
                                                    @endif
                                                @else
                                                    Rp{{ number_format($promo->value, 0, ',', '.') }}
                                                @endif
                                            </td>
                                            <td>
                                                {{ $promo->used_count }} / 
                                                {{ $promo->max_uses > 0 ? $promo->max_uses : '∞' }}
                                            </td>
                                            <td>
                                                @if($promo->is_active)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-danger">Non-aktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($promo->expires_at)
                                                    <small class="d-block">Berlaku hingga:</small>
                                                    {{ $promo->expires_at->format('d M Y') }}
                                                @else
                                                    Tidak ada batas
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.promos.edit', $promo) }}" 
                                                       class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-edit me-1"></i>Edit
                                                    </a>
                                                    <form action="{{ route('admin.promos.destroy', $promo) }}" method="POST" 
                                                          class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('Yakin ingin menghapus kode promo ini?')">
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
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                            <h4>Belum Ada Kode Promo</h4>
                            <p class="text-muted mb-4">Buat kode promo pertama Anda untuk memberikan diskon kepada pelanggan</p>
                            <a href="{{ route('admin.promos.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Tambah Kode Promo
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection