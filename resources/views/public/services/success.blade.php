@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <div class="card shadow-sm border-0 p-5 d-inline-block">
        <div class="display-1 text-success mb-3"><i class="fas fa-check-circle"></i></div>
        <h2 class="fw-bold">Order Received!</h2>
        <p class="lead">Thank you. The management will contact you shortly for payment.</p>
        <a href="/" class="btn btn-primary mt-3">Back to Home</a>
    </div>
</div>
@endsection