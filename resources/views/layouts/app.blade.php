<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'N-Shop - Your Online Shopping Destination')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/N-Shop-logo.png') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @stack('styles')
</head>
<body data-page="@yield('page-identifier', 'default')" class="@yield('body-class')">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container-fluid px-4">
            <!-- Left Side: Logo + Extended Search -->
            <div class="d-flex align-items-center flex-grow-1 me-4">
                <!-- Logo -->
                <a class="navbar-brand me-4" href="{{ route('index_product') }}">
                    <i class="fas fa-shopping-bag me-2"></i>N-Shop
                </a>

                <!-- Extended Search Bar -->
                <div class="search-container-extended">
                    <form action="{{ route('index_product') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="search-input-extended" 
                               placeholder="Cari produk..." value="{{ request('search') }}">
                        <button class="btn btn-search" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Right Side: Navigation Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Mobile Search -->
                <div class="d-lg-none mb-3 mt-2">
                    <form action="{{ route('index_product') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control search-input" 
                                   placeholder="Cari produk..." value="{{ request('search') }}">
                            <button class="btn btn-search" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Navigation Links -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('index_product') }}">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>

                    @auth
                        <!-- Cart (HANYA UNTUK USER) -->
                        @if(!auth()->user()->is_admin)
                            <li class="nav-item">
                                <a class="nav-link position-relative" href="{{ route('show_cart') }}">
                                    <i class="fas fa-shopping-cart me-1"></i>Cart
                                    @if(auth()->user()->carts->count() > 0)
                                        <span class="badge bg-danger">{{ auth()->user()->carts->count() }}</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                        
                        <!-- Wishlist (HANYA UNTUK USER) -->
                        @if(!auth()->user()->is_admin)
                            <li class="nav-item">
                                <a class="nav-link position-relative" href="{{ route('wishlist.index') }}">
                                    <i class="fas fa-heart me-1"></i>Wishlist
                                </a>
                            </li>
                        @endif
                        
                        <!-- Orders (HANYA UNTUK USER) -->
                        @if(!auth()->user()->is_admin)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('index_order') }}">
                                    <i class="fas fa-receipt me-1"></i>Orders
                                </a>
                            </li>
                        @endif
                        
                        <!-- Admin Menu (HANYA UNTUK ADMIN) -->
                        @if(auth()->user()->is_admin)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.orders') }}">
                                    <i class="fas fa-shopping-cart me-1"></i>Kelola Pesanan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.category.index') }}">
                                    <i class="fas fa-tags me-1"></i>Kategori
                                </a>
                            </li>
                        @endif
                        
                        <!-- Profile Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" 
                               data-bs-toggle="dropdown">
                                @if(auth()->user()->avatar)
                                    <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Avatar" 
                                         class="rounded-circle me-1" width="25" height="25">
                                @else
                                    <i class="fas fa-user-circle me-1"></i>
                                @endif
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('show_profile') }}">
                                    <i class="fas fa-user me-2"></i>Profile</a></li>
                                
                                <!-- Address hanya untuk user -->
                                @if(!auth()->user()->is_admin)
                                    <li><a class="dropdown-item" href="{{ route('address.index') }}">
                                        <i class="fas fa-map-marker-alt me-2"></i>Addresses</a></li>
                                @endif
                                
                                <!-- Admin menu di dropdown -->
                                @if(auth()->user()->is_admin)
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.create_product') }}">
                                        <i class="fas fa-plus me-2"></i>Add Product</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.category.index') }}">
                                        <i class="fas fa-tags me-2"></i>Categories</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.users') }}">
                                        <i class="fas fa-users me-2"></i>Kelola User</a></li>
                                @endif
                                
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <!-- Guest Menu -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main style="margin-top: 40px; min-height: calc(100vh - 200px);">
        <!-- Alert Messages -->
        @if(session('success'))
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-custom">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5><i class="fas fa-shopping-bag me-2"></i>N-Shop</h5>
                    <p>Platform belanja online terpercaya dengan berbagai produk berkualitas dan harga terjangkau.</p>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('index_product') }}" class="text-white-50">Products</a></li>
                        <li><a href="#" class="text-white-50">About Us</a></li>
                        <li><a href="#" class="text-white-50">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6>Categories</h6>
                    <ul class="list-unstyled">
                        @foreach(\App\Models\Category::withCount('products')->take(4)->get() as $category)
                            <li>
                                <a href="{{ route('product.by_category', $category) }}" class="text-white-50">
                                    {{ $category->name }} ({{ $category->products_count }})
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6>Follow Us</h6>
                    <div class="d-flex">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} N-Shop. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Vite JS -->
    @vite(['resources/js/app.js'])
</body>
</html>