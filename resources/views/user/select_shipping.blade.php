@extends('layouts.app')

@section('title', 'Pilih Alamat Pengiriman - N-Shop')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-truck me-2"></i>Alamat Pengiriman</h2>
                <a href="{{ route('address.create') }}" class="btn btn-outline-primary">
                    <i class="fas fa-plus me-2"></i>Tambah Alamat Baru
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">Pilih Alamat Pengiriman</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('checkout') }}" method="POST" id="checkoutForm">
                        @csrf
                        <input type="hidden" name="weight" value="{{ $totalWeight }}">
                        
                        <div class="address-list">
                            @foreach($addresses as $address)
                                <div class="form-check custom-address-item mb-3 p-3 border rounded">
                                    <input class="form-check-input" type="radio" name="address_id" 
                                           id="address{{ $address->id }}" value="{{ $address->id }}"
                                           data-province="{{ $address->province }}"
                                           data-country="{{ $address->country }}"
                                           {{ $loop->first ? 'checked' : '' }}>
                                    <label class="form-check-label ms-2" for="address{{ $address->id }}">
                                        <strong>{{ $address->name }}</strong>
                                        <p class="mb-1">{{ $address->phone }}</p>
                                        <p class="mb-1">
                                            {{ $address->address }}, 
                                            {{ $address->city ?: $address->district }}, 
                                            {{ $address->province }}, 
                                            {{ $address->postal_code }}, 
                                            {{ $address->country }}
                                        </p>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        
                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="fas fa-check me-2"></i>Gunakan Alamat Ini
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">Ringkasan Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Subtotal ({{ $carts->count() }} item)</span>
                        <span>Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                    </div>
                    
                    @if(session('promo_id'))
                    <div class="d-flex justify-content-between mb-3 text-success">
                        <span>Diskon</span>
                        <span>- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span>Ongkos Kirim</span>
                        <span id="shippingCost" class="text-dark">Menghitung...</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <strong>Total</strong>
                        <div class="text-end">
                            <strong class="text-primary fs-5" id="grandTotal">
                                Menghitung...
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="alert alert-info small">
        <i class="fas fa-info-circle me-2"></i>
        Ongkos kirim dihitung berdasarkan alamat tujuan dan berat total pesanan 
        <strong>{{ number_format($totalWeight, 2) }} kg</strong>.
    </div>
</div>

<!-- Menyimpan data untuk JavaScript -->
<input type="hidden" id="js-totalPrice" value="{{ $totalPrice }}">
<input type="hidden" id="js-discount" value="{{ $discount ?? 0 }}">
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Fungsi untuk menghitung ongkir
    function calculateShipping() {
        const addressId = $('input[name="address_id"]:checked').val();
        const totalWeight = $('input[name="weight"]').val();
        
        if (!addressId) return;
        
        // Tampilkan loading
        $('#shippingCost').html('<i class="fas fa-spinner fa-spin"></i> Menghitung...');
        $('#grandTotal').html('<i class="fas fa-spinner fa-spin"></i>');
        
        // Kirim request AJAX untuk menghitung ongkir
        $.ajax({
            url: '{{ route("calculate.shipping") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                address_id: addressId,
                weight: totalWeight
            },
            success: function(response) {
                // Update tampilan ongkir
                $('#shippingCost').text(response.formatted_cost);
                
                // Ambil nilai dari elemen tersembunyi
                const subtotal = parseFloat(document.getElementById('js-totalPrice').value);
                const discount = parseFloat(document.getElementById('js-discount').value);
                const shippingCost = response.shipping_cost;
                const grandTotal = subtotal - discount + shippingCost;
                
                $('#grandTotal').text('Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal));
                
                // Simpan ongkir di form
                // Hapus dulu input hidden sebelumnya jika ada
                $('#checkoutForm input[name="shipping_cost"]').remove();
                
                $('<input>').attr({
                    type: 'hidden',
                    name: 'shipping_cost',
                    value: shippingCost
                }).appendTo('#checkoutForm');
            },
            error: function() {
                $('#shippingCost').text('Gagal menghitung');
                $('#grandTotal').text('Gagal menghitung total');
            }
        });
    }
    
    // Hitung ongkir saat pertama kali load halaman
    calculateShipping();
    
    // Hitung ulang ongkir saat pilihan alamat berubah
    $('input[name="address_id"]').on('change', function() {
        calculateShipping();
    });
});
</script>
@endpush