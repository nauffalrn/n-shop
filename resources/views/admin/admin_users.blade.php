@extends('layouts.app')

@section('title', 'Kelola User - N-Shop Admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-users me-2"></i>Kelola User
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-body">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Total Pesanan</th>
                                        <th>Keranjang</th>
                                        <th>Wishlist</th>
                                        <th>Bergabung</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($user->avatar)
                                                        <img src="{{ Storage::url($user->avatar) }}" 
                                                             alt="Avatar" class="rounded-circle me-2" 
                                                             width="30" height="30">
                                                    @else
                                                        <i class="fas fa-user-circle fa-lg me-2 text-muted"></i>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $user->name }}</strong>
                                                        @if($user->phone)
                                                            <br><small class="text-muted">{{ $user->phone }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ $user->orders_count }} pesanan</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $user->carts_count }} item</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning">{{ $user->wishlists_count }} item</span>
                                            </td>
                                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                @if($user->email_verified_at)
                                                    <span class="badge bg-success">Terverifikasi</span>
                                                @else
                                                    <span class="badge bg-danger">Belum Verifikasi</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $users->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h4>Belum Ada User</h4>
                            <p class="text-muted">User yang mendaftar akan muncul di sini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection