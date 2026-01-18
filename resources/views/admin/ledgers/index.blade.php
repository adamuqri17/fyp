@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between mb-4">
        <h3>Manage Ledger Products</h3>
        <a href="{{ route('admin.ledgers.create') }}" class="btn btn-primary">Add New Product</a>
    </div>

    <div class="card shadow-sm border-0">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Name</th>
                    <th>Material</th>
                    <th>Price (RM)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ledgers as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->material }}</td>
                    <td>{{ number_format($item->price, 2) }}</td>
                    <td>
                        <form action="{{ route('admin.ledgers.destroy', $item->ledger_id) }}" method="POST" onsubmit="return confirm('Delete this product?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection