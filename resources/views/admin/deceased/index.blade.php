@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Deceased Registry</h1>
        <a href="{{ route('admin.deceased.create') }}" class="btn btn-success">
            <i class="fas fa-user-plus me-1"></i> Register New Burial
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('admin.deceased.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by Name or IC Number..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">Search</button>
                    @if(request('search'))
                        <a href="{{ route('admin.deceased.index') }}" class="btn btn-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>IC Number</th>
                        <th>Date of Death</th>
                        <th>Grave Location</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deceaseds as $person)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold">{{ $person->full_name }}</div>
                            <small class="text-muted">{{ $person->gender }}</small>
                        </td>
                        <td>{{ $person->ic_number }}</td>
                        <td>{{ \Carbon\Carbon::parse($person->date_of_death)->format('d M Y') }}</td>
                        <td>
                            @if($person->grave)
                                <span class="badge bg-danger">
                                    {{ $person->grave->section->section_name ?? 'Gen' }} - Plot {{ $person->grave->grave_id }}
                                </span>
                            @else
                                <span class="badge bg-secondary">Unassigned</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.deceased.edit', $person->deceased_id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.deceased.destroy', $person->deceased_id) }}" method="POST" class="d-inline" onsubmit="return confirm('WARNING: This will delete the record and mark the grave as Available. Continue?');">
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
                            No records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $deceaseds->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection