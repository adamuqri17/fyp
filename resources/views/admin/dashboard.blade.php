@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-dark">Admin Dashboard</h2>
            <p class="text-muted">Welcome back, {{ Auth::guard('admin')->user()->name }}</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-4 border-primary h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted small fw-bold">Total Plots</h6>
                    <h2 class="display-4 fw-bold text-primary mb-0">{{ $totalGraves }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-4 border-success h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted small fw-bold">Available</h6>
                    <h2 class="display-4 fw-bold text-success mb-0">{{ $available }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-4 border-danger h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted small fw-bold">Occupied</h6>
                    <h2 class="display-4 fw-bold text-danger mb-0">{{ $occupied }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-4 border-warning h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted small fw-bold">Reserved</h6>
                    <h2 class="display-4 fw-bold text-warning mb-0">{{ $reserved }}</h2>
                </div>
            </div>
        </div>
    </div>

    <h5 class="fw-bold text-dark mb-3">Quick Actions</h5>
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold"><i class="fas fa-map-marked-alt text-success me-2"></i>Map Manager</h5>
                        <p class="text-muted small mb-0">Manage plots visually.</p>
                    </div>
                    <a href="{{ route('admin.map.manager') }}" class="btn btn-outline-success">Go to Map</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold"><i class="fas fa-user-plus text-primary me-2"></i>Register Burial</h5>
                        <p class="text-muted small mb-0">Add a new deceased record.</p>
                    </div>
                    <a href="{{ route('admin.deceased.create') }}" class="btn btn-primary">Register New</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">Recently Updated Records</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Date of Death</th>
                        <th>Grave</th>
                        <th>Burial Date</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentDeceased as $person)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">{{ $person->full_name }}</td>
                        <td>{{ \Carbon\Carbon::parse($person->date_of_death)->format('d M Y') }}</td>
                        <td>
                            <span class="badge bg-danger">
                                {{ $person->grave->section->section_name ?? 'Gen' }} - Plot {{ $person->grave->grave_id }}
                            </span>
                        </td>
                        <td class="small text-muted">
                            {{ $person->burial_date ? \Carbon\Carbon::parse($person->burial_date)->format('d M Y') : 'N/A' }}
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.deceased.edit', $person->deceased_id) }}" class="btn btn-sm btn-light text-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No recent records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white text-center">
            <a href="{{ route('admin.deceased.index') }}" class="text-decoration-none fw-bold small">View All Records <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
    </div>
</div>
@endsection