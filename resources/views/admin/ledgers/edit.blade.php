@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card shadow-sm border-0 col-md-8 mx-auto">
        <div class="card-header bg-warning text-dark fw-bold">
            <i class="fas fa-edit me-2"></i>Edit Ledger Product
        </div>
        <div class="card-body">
            <form action="{{ route('admin.ledgers.update', $ledger->ledger_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Product Name</label>
                    <input type="text" name="name" class="form-control" required value="{{ $ledger->name }}">
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Material</label>
                    <select name="material" class="form-select">
                        <option value="Granite" {{ $ledger->material == 'Granite' ? 'selected' : '' }}>Granite</option>
                        <option value="Marble" {{ $ledger->material == 'Marble' ? 'selected' : '' }}>Marble</option>
                        <option value="Terrazzo" {{ $ledger->material == 'Terrazzo' ? 'selected' : '' }}>Terrazzo</option>
                        <option value="Wood" {{ $ledger->material == 'Wood' ? 'selected' : '' }}>Wood</option>
                        <option value="Ceramic" {{ $ledger->material == 'Ceramic' ? 'selected' : '' }}>Ceramic</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Price (RM)</label>
                    <input type="number" name="price" class="form-control" required value="{{ $ledger->price }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Current Image</label>
                    <div class="mb-2">
                        @if($ledger->picture && $ledger->picture != 'images/placeholder.jpg')
                            <img src="{{ asset('storage/' . $ledger->picture) }}" alt="Current Image" class="img-thumbnail" style="height: 100px;">
                        @else
                            <span class="text-muted small">No custom image uploaded. (Using Placeholder)</span>
                        @endif
                    </div>
                    <label class="form-label fw-bold small text-muted">Upload New Image (Optional)</label>
                    <input type="file" name="picture" class="form-control" accept="image/*">
                    <small class="text-muted">Leave empty to keep current image.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ $ledger->description }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.ledgers.index') }}" class="btn btn-secondary">Cancel</a>
                    <button class="btn btn-warning fw-bold">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection