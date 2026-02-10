@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark"><i class="fas fa-box-open me-2"></i>Ledger Products</h3>
        <a href="{{ route('admin.ledgers.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Add New Product
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Image</th>
                            <th>Product Name</th>
                            <th>Material</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ledgers as $ledger)
                        <tr>
                            <td class="ps-4">
                                @if($ledger->picture && str_starts_with($ledger->picture, 'ledgers/'))
                                    <img src="{{ asset('storage/' . $ledger->picture) }}" class="rounded shadow-sm" width="50" height="50" style="object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="fw-bold">{{ $ledger->name }}</td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-secondary border">{{ $ledger->material }}</span></td>
                            <td class="text-success fw-bold">RM {{ number_format($ledger->price, 2) }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.ledgers.edit', $ledger->ledger_id) }}" class="btn btn-sm btn-warning text-dark">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    <form action="{{ route('admin.ledgers.destroy', $ledger->ledger_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                No products found. Click "Add New Product" to start.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection