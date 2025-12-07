@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold">Search Results</h2>
            <p class="text-muted">Showing results for: <span class="text-dark fw-bold">"{{ request('keyword') ?? '...' }}"</span></p>
        </div>
        <div class="col-md-4 text-end">
            <a href="/" class="btn btn-outline-secondary">Back to Search</a>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Deceased Name</th>
                            <th>IC Number</th>
                            <th>Date of Death</th>
                            <th>Grave Location</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">Ahmad Bin Abu</div>
                                <small class="text-muted">ID: 12345</small>
                            </td>
                            <td>800101-10-5555</td>
                            <td>12 Jan 2024</td>
                            <td><span class="badge bg-success">Section A - Plot 15</span></td>
                            <td class="text-end pe-4">
                                <a href="#" class="btn btn-sm btn-primary">
                                    <i class="bi bi-geo-alt"></i> View on Map
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">Siti Aminah Binti Ali</div>
                                <small class="text-muted">ID: 67890</small>
                            </td>
                            <td>550505-10-1234</td>
                            <td>20 Feb 2023</td>
                            <td><span class="badge bg-success">Section B - Plot 08</span></td>
                            <td class="text-end pe-4">
                                <a href="#" class="btn btn-sm btn-primary">View on Map</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection