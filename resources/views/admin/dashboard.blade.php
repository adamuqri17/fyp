@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Administrator Dashboard</h1>
        <div>
            <a href="{{ url('/admin/graves/create') }}" class="btn btn-success me-2">+ Register New Grave</a>
            <a href="{{ url('/admin/deceased/create') }}" class="btn btn-primary me-2">+ Register Burial</a>
            
            <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 border-start border-4 border-primary">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Total Graves</span>
                        <h3 class="mb-0 fw-bold">120</h3>
                    </div>
                    <div class="bg-light rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        📊
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 border-start border-4 border-danger">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Occupied Plots</span>
                        <h3 class="mb-0 fw-bold">85</h3>
                    </div>
                    <div class="bg-light rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        🪦
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 border-start border-4 border-success">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Available Plots</span>
                        <h3 class="mb-0 fw-bold">35</h3>
                    </div>
                    <div class="bg-light rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        ✅
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recent Burial Records</h6>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Burial Date</th>
                        <th>Section</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Ali Bin Abu</td>
                        <td>2024-03-15</td>
                        <td>Section A</td>
                        <td>
                            <button class="btn btn-sm btn-info text-white">Edit</button>
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection