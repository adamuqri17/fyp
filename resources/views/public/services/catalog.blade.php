@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-success">Tempahan Batu Nisan</h2>
        <p class="text-muted">High quality materials for your loved ones.</p>
    </div>

    <div class="row g-4">
        @foreach($ledgers as $item)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div style="height: 200px; overflow: hidden;" class="bg-light d-flex align-items-center justify-content-center">
                    @if($item->picture && str_starts_with($item->picture, 'ledgers/'))
                        <img src="{{ asset('storage/' . $item->picture) }}" alt="{{ $item->name }}" class="w-100 h-100" style="object-fit: cover;">
                    @else
                        <div class="text-muted text-center">
                            <i class="fas fa-image fa-3x mb-2"></i><br>
                            No Image
                        </div>
                    @endif
                </div>

                <div class="card-body text-center p-4">
                    <h5 class="fw-bold">{{ $item->name }}</h5>
                    <p class="text-muted small">{{ $item->material }}</p>
                    <hr>
                    <h3 class="text-success fw-bold">RM {{ number_format($item->price, 0) }}</h3>
                    <p class="small text-muted">{{ Str::limit($item->description, 50) }}</p>
                    <a href="{{ route('public.ledgers.order', $item->ledger_id) }}" class="btn btn-success w-100 mt-2">Order Now</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection