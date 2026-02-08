<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TPIRS - Cemetery Plotting System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="d-flex flex-column h-100">

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/"><i class="fas fa-mosque me-2"></i>TPIRS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                
                {{-- =========================================================
                     1. ADMIN NAVBAR
                     ========================================================= --}}
                @auth('admin')
                    
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Home</a>
                    </li>

                    {{-- REMOVED: Grave Plotting Navigation (Moved to Map Button) --}}

                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('admin.map.manager') ? 'active' : '' }}" href="{{ route('admin.map.manager') }}">Map Overview</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('admin.deceased.index') ? 'active' : '' }}" href="{{ route('admin.deceased.index') }}">Deceased List</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ Request::is('admin/ledgers*') || Request::is('admin/orders*') ? 'active' : '' }}" href="#" id="ledgerDropdown" role="button" data-bs-toggle="dropdown">
                            Ledger Management
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="ledgerDropdown">
                            <li><a class="dropdown-item" href="{{ route('admin.ledgers.index') }}">Products</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}">Customer Orders</a></li>
                        </ul>
                    </li>

                    <li class="nav-item border-start ms-2 ps-2"></li>

                    <li class="nav-item">
                        <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger mt-1 ms-1">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </li>

                {{-- =========================================================
                     2. PUBLIC NAVBAR (Guest Only)
                     ========================================================= --}}
                @else
                    <li class="nav-item"><a class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="/">Halaman Utama</a></li>
                    <li class="nav-item"><a class="nav-link {{ Request::is('map') ? 'active' : '' }}" href="/map">Peta Kubur</a></li>
                    <li class="nav-item"><a class="nav-link {{ Request::routeIs('public.services.index') ? 'active' : '' }}" href="{{ route('public.services.index') }}">Tempahan Batu Nisan</a></li>
                    <li class="nav-item"><a class="nav-link {{ Request::is('contact') ? 'active' : '' }}" href="/contact">Hubungi Kami</a></li>
                @endauth

            </ul>
        </div>
    </div>
</nav>

<main>
    @yield('content')
</main>

<footer class="footer-modern mt-auto pt-5 pb-3">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-5 col-md-6">
                <h5 class="text-white fw-bold mb-3"><i class="fas fa-moon me-2 text-success"></i> TPIRS</h5>
                <p class="mb-4">
                    Sistem Pengurusan Tanah Perkuburan Islam Raudhatul Sa’adah. 
                </p>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-title">Quick Navigation</h6>
                <ul class="list-unstyled">
                    <li><a href="/" class="footer-link"><i class="fas fa-angle-right me-2 text-success"></i>Carian Plot Kubur</a></li>
                    <li><a href="/map" class="footer-link"><i class="fas fa-angle-right me-2 text-success"></i>Peta Kubur</a></li>
                    <li><a href="{{ route('public.services.index') }}" class="footer-link"><i class="fas fa-angle-right me-2 text-success"></i>Tempahan Batu Nisan</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-6">
                <h6 class="footer-title">Hubungi Kami</h6>
                <ul class="list-unstyled">
                    <li class="mb-3 d-flex">
                        <i class="fas fa-map-marker-alt text-success mt-1 me-3"></i>
                        <span>Kampung Johan Setia, 41200 Klang, Selangor</span>
                    </li>
                    <li class="mb-3 d-flex">
                        <i class="fas fa-phone text-success mt-1 me-3"></i>
                        <span>+60 3-3323 1234</span>
                    </li>
                </ul>
            </div>
        </div>
        <hr class="footer-divider my-4">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 small">&copy; 2025 TPIRS. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                @guest('admin')
                    <a href="{{ route('admin.login') }}" class="staff-login-btn">
                        <i class="fas fa-lock me-1"></i> Staff Login
                    </a>
                @else
                    <span class="text-success small"><i class="fas fa-check-circle me-1"></i> Logged in as Staff</span>
                @endguest
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>