@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <!-- Left Content -->
            <div class="col-lg-6">
                <span class="badge bg-success mb-3">Tanah Perkuburan Islam Raudhatul Sa'adah</span>
                <h1>Find Your Cemetery Plot with Ease</h1>
                <p>Search and locate available plots with accurate mapping and verified records. TPIRS makes cemetery management transparent and accessible.</p>

                <!-- Search Form -->
                <div class="search-card mt-4">
                    <h5>Start Your Search</h5>
                    <div class="row g-2 mt-2">
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="Cemetery Location">
                        </div>
                        <div class="col-md-6">
                            <select class="form-select">
                                <option>All Sections</option>
                                <option>Section A</option>
                                <option>Section B</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="Plot No.">
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-success w-100">Find Plot</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Image Card -->
            <div class="col-lg-6">
                <div class="plot-card">
                    <img src="/images/kubur.jpg" alt="Cemetery Plot">
                    <div class="p-3">
                        <h5>Available Plot - Section A</h5>
                        <p class="mb-1">Row 3, Plot No. 15</p>
                        <small class="text-muted">Klang Muslim Cemetery</small>
                    </div>
                </div>

                <!-- Advisor Card -->
                <div class="advisor-card d-flex align-items-center">
                    <img src="https://via.placeholder.com/50" class="rounded-circle me-3" alt="Advisor">
                    <div>
                        <h6 class="mb-0">Ahmad Ali</h6>
                        <small>Cemetery Officer</small><br>
                        ⭐ 5.0 (120 reviews)
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
