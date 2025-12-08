@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Manage Grave Plots</h1>
        <a href="{{ route('admin.graves.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-1"></i> Add New Plot
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Section</th>
                        <th>Coordinates (Lat, Long)</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($graves as $grave)
                    <tr>
                        <td class="ps-4 fw-bold">#{{ $grave->grave_id }}</td>
                        <td>{{ $grave->section->section_name ?? 'N/A' }}</td>
                        <td class="small font-monospace">{{ $grave->latitude }}, {{ $grave->longitude }}</td>
                        <td>
                            @if($grave->status == 'available')
                                <span class="badge bg-success">Available</span>
                            @elseif($grave->status == 'occupied')
                                <span class="badge bg-danger">Occupied</span>
                            @else
                                <span class="badge bg-warning text-dark">Reserved</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.graves.edit', $grave->grave_id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.graves.destroy', $grave->grave_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            No graves found. Click "Add New Plot" to start.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $graves->links() }} </div>
    </div>
</div>
@endsection