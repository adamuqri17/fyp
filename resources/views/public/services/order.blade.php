@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Order Confirmation</h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-light border text-center mb-4">
                        <strong>Item:</strong> {{ $ledger->name }}<br>
                        <strong>Price:</strong> RM {{ number_format($ledger->price, 2) }}
                    </div>

                    <form action="{{ route('public.ledgers.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="ledger_id" value="{{ $ledger->ledger_id }}">
                        <input type="hidden" name="amount" value="{{ $ledger->price }}">

                        <div class="mb-3">
                            <label class="fw-bold">Grave Plot Number</label>
                            <input type="number" name="grave_id" class="form-control" required placeholder="e.g. 5">
                            <small class="text-muted">Enter the Plot ID from the map.</small>
                            @error('grave_id') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">Your Name</label>
                            <input type="text" name="buyer_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">Contact Number</label>
                            <input type="text" name="buyer_phone" class="form-control" required placeholder="012-3456789">
                        </div>

                        <button class="btn btn-success w-100 py-2">Confirm Order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection