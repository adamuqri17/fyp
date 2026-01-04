@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-success">Direktori Arwah</h2>
        <p class="text-muted">Senarai penuh rekod kematian dan lokasi pusara.</p>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Nama Penuh</th>
                            <th>Tarikh Meninggal</th>
                            <th>Lokasi Pusara</th>
                            <th class="text-end pe-4">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deceaseds as $person)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $person->full_name }}</div>
                                <small class="text-muted">
                                    <i class="fas fa-id-card me-1"></i> {{ $person->ic_number }}
                                </small>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($person->date_of_death)->format('d M Y') }}
                            </td>
                            <td>
                                @if($person->grave)
                                    <span class="badge bg-success">
                                        {{ $person->grave->section->section_name ?? 'Zon A' }} - Plot {{ $person->grave->grave_id }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Tidak Ditetapkan</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @if($person->grave)
                                    <a href="{{ route('map.public') }}?focus={{ $person->grave->grave_id }}" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-map-marked-alt me-1"></i> Lokasi
                                    </a>
                                @else
                                    <button class="btn btn-sm btn-light" disabled>Tiada Lokasi</button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                Tiada rekod dijumpai.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card-footer bg-white d-flex justify-content-center py-3">
            {{ $deceaseds->links() }}
        </div>
    </div>
</div>
@endsection