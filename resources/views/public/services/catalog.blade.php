@extends('layouts.app')

@section('content')
<div class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="fw-bold text-success display-5">Tempahan Batu Nisan</h1>
            <p class="lead text-muted">Premium quality craftsmanship for your loved ones' final resting place.</p>
            <div style="width: 60px; height: 4px; background-color: #198754; margin: 20px auto;"></div>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach($ledgers as $item)
            <div class="col-lg-4 col-md-6 col-sm-10">
                <div class="card h-100 shadow-sm border-0 hover-shadow transition-all overflow-hidden">
                    <div class="position-relative bg-white d-flex align-items-center justify-content-center p-3" style="height: 300px;">
                        @if($item->picture && str_starts_with($item->picture, 'ledgers/'))
                            {{-- MODIFIED: object-fit: contain ensures the whole image is visible --}}
                            <img src="{{ asset('storage/' . $item->picture) }}" 
                                 alt="{{ $item->name }}" 
                                 class="img-fluid" 
                                 style="max-height: 100%; max-width: 100%; object-fit: contain; transition: transform 0.3s ease;">
                        @else
                            <div class="text-center text-muted opacity-50">
                                <i class="fas fa-monument fa-4x mb-2"></i>
                                <p class="small mb-0">No Preview</p>
                            </div>
                        @endif
                        
                        <span class="position-absolute top-0 end-0 bg-success text-white px-3 py-1 m-3 rounded-pill small fw-bold shadow-sm" style="z-index: 10;">
                            {{ $item->material }}
                        </span>
                    </div>

                    <div class="card-body text-center p-4 d-flex flex-column bg-white">
                        <h5 class="fw-bold text-dark mb-2">{{ $item->name }}</h5>
                        <p class="small text-muted mb-4" style="min-height: 40px;">
                            {{ Str::limit($item->description, 100) }}
                        </p>
                        
                        <div class="mt-auto">
                            <h3 class="text-success fw-bold mb-3">RM {{ number_format($item->price, 0) }}</h3>
                            <a href="{{ route('public.ledgers.order', $item->ledger_id) }}" class="btn btn-success w-100 fw-bold rounded-pill shadow-sm py-2">
                                View & Order <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
    }
    .hover-shadow img:hover {
        transform: scale(1.05);
    }
    .transition-all {
        transition: all 0.3s ease;
    }
</style>
@endsection