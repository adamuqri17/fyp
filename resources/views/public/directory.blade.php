@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
                <div class="mb-3 mb-md-0">
                    <h2 class="fw-bold text-success mb-0">Direktori Arwah</h2>
                    <p class="text-muted small mb-0">Senarai penuh rekod kematian & lokasi.</p>
                </div>
                
                <a href="/" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left fa-sm me-1"></i> Kembali
                </a>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <form action="{{ route('public.directory') }}" method="GET">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" 
                                   placeholder="Cari Nama / No. KP..." 
                                   value="{{ request('search') }}">
                            <button class="btn btn-success fw-bold px-4" type="submit">Cari</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-uppercase small text-muted">Nama Arwah</th>
                                <th class="py-3 text-uppercase small text-muted">Tarikh Meninggal</th>
                                <th class="py-3 text-uppercase small text-muted">Lokasi</th>
                                <th class="text-end pe-4 py-3 text-uppercase small text-muted"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deceaseds as $person)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark">{{ $person->full_name }}</div>
                                    <small class="text-muted" style="font-size: 0.8rem;">
                                        <i class="fas fa-id-card me-1 text-success"></i> {{ $person->ic_number }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-normal">
                                        {{ \Carbon\Carbon::parse($person->date_of_death)->format('d M Y') }}
                                    </span>
                                </td>
                                <td>
                                    @if($person->grave)
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 0.7rem;">
                                                <i class="fas fa-map-pin"></i>
                                            </div>
                                            <span class="fw-bold text-dark small">
                                                {{ $person->grave->section->section_name ?? 'Zon A' }} - Plot {{ $person->grave->grave_id }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-muted small fst-italic">Belum Ditetapkan</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    @if($person->grave)
                                        <a href="{{ route('map.public') }}?focus={{ $person->grave->grave_id }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <small><i class="fas fa-search-location me-1"></i> Lihat Peta</small>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted mb-2"><i class="fas fa-inbox fa-3x opacity-25"></i></div>
                                    <p class="text-muted mb-0">Tiada rekod dijumpai.</p>
                                    @if(request('search'))
                                        <a href="{{ route('public.directory') }}" class="btn btn-link btn-sm text-decoration-none">Reset Carian</a>
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer bg-white py-3 d-flex justify-content-center">
                    {{ $deceaseds->appends(request()->query())->links() }}
                </div>
            </div>

        </div>
    </div>
</div>
@endsection