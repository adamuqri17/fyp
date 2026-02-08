@extends('layouts.app')

@section('content')
<div class="container py-5 d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="card shadow-lg border-0 p-5 text-center" style="max-width: 600px;">
        <div class="mb-4">
            <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                <i class="fas fa-tools fa-4x text-success"></i>
            </div>
        </div>
        
        <h2 class="fw-bold text-dark mb-2">Payment Verified!</h2>
        <p class="text-muted mb-4">Your order has been saved and is now <b>Pending Installation</b>.</p>

        @if(session('order_id'))
        <div class="alert alert-light border border-success d-inline-block px-4 py-2 mb-4 rounded-pill">
            <span class="text-muted small text-uppercase fw-bold me-2">Order Reference:</span>
            <span class="text-success fw-bold fs-5">#{{ str_pad(session('order_id'), 6, '0', STR_PAD_LEFT) }}</span>
        </div>
        @endif

        <div class="bg-light p-4 rounded text-start mb-4">
            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-clipboard-check me-2"></i>Status: Installation Pending</h6>
            <ol class="mb-0 text-muted ps-3 small">
                <li class="mb-2">We have successfully received your payment.</li>
                <li class="mb-2">Your order has been added to our <b>Installation Queue</b>.</li>
                <li class="mb-0">Estimated completion: <b>7-14 working days</b>.</li>
            </ol>
        </div>

        <div class="d-grid gap-2">
            <a href="/" class="btn btn-primary">Return to Home</a>
            <a href="{{ route('public.services.index') }}" class="btn btn-outline-secondary">Back to Catalog</a>
        </div>
    </div>
</div>
@endsection