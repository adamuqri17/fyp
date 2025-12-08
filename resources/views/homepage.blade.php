@extends('layouts.app')

@section('content')

<section class="home-hero">
    <div class="container">
        <span class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill fw-bold">
            <i class="fas fa-star me-1"></i> Sistem Pengurusan Kubur Digital
        </span>
        <h1>Tanah Perkuburan Islam <br> Raudhatul Sa'adah</h1>
        <p class="lead">
            Memudahkan waris mencari lokasi pusara dengan teknologi pemetaan geospatial yang tepat, 
            sistematik, dan patuh syariah.
        </p>
    </div>
</section>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="search-container-floating">
                <div class="text-center">
                    <h6 class="search-title">Carian Pantas Pusara</h6>
                </div>
                
                <form action="{{ route('grave.search') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-lg-8">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" name="keyword" class="form-control bg-light border-start-0 ps-0" 
                                       placeholder="Masukkan Nama Arwah atau No. Kad Pengenalan..." required>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold">
                                <i class="fas fa-search-location me-2"></i> Cari Lokasi
                            </button>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i> 
                            Contoh: "Ahmad bin Ali" atau "800101-10-5555"
                        </small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <h4 class="fw-bold mb-3">Pemetaan Digital</h4>
                <p class="text-muted mb-4">
                    Lihat paparan peta digital interaktif untuk mengetahui kedudukan tepat plot kubur di kawasan tanah perkuburan.
                </p>
                <a href="/map" class="text-success fw-bold text-decoration-none stretched-link">
                    Lihat Peta <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <h4 class="fw-bold mb-3">Direktori Arwah</h4>
                <p class="text-muted mb-4">
                    Pangkalan data lengkap yang menyimpan rekod kematian dan lokasi pengebumian secara sistematik dan selamat.
                </p>
                <a href="#" class="text-success fw-bold text-decoration-none stretched-link">
                    Semak Rekod <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-hands-helping"></i>
                </div>
                <h4 class="fw-bold mb-3">Khidmat Waris</h4>
                <p class="text-muted mb-4">
                    Hubungi pihak pengurusan untuk urusan tempahan plot, penyelenggaraan, atau bantuan carian kubur.
                </p>
                <a href="/contact" class="text-success fw-bold text-decoration-none stretched-link">
                    Hubungi Kami <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<section class="stats-section">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-4 stat-item">
                <h2>1,250</h2>
                <p>Jumlah Jenazah Dikebumikan</p>
            </div>
            <div class="col-md-4 stat-item">
                <h2>85</h2>
                <p>Plot Kosong (Tahun Ini)</p>
            </div>
            <div class="col-md-4 stat-item">
                <h2>4</h2>
                <p>Zon / Blok Utama</p>
            </div>
        </div>
    </div>
</section>

@endsection