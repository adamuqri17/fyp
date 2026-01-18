@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card shadow-sm border-0 col-md-8 mx-auto">
        <div class="card-header bg-primary text-white">Add New Ledger Type</div>
        <div class="card-body">
            <form action="{{ route('admin.ledgers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label>Product Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Standard Granite">
                </div>
                
                <div class="mb-3">
                    <label>Material</label>
                    <select name="material" class="form-select">
                        <option>Granite</option>
                        <option>Marble</option>
                        <option>Terrazzo</option>
                        <option>Wood</option>
                        <option>Ceramic</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Price (RM)</label>
                    <input type="number" name="price" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Product Image</label>
                    <input type="file" name="picture" class="form-control" accept="image/*">
                    <small class="text-muted">Supported formats: JPG, PNG. Max size: 2MB.</small>
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <button class="btn btn-success">Save Product</button>
            </form>
        </div>
    </div>
</div>
@endsection