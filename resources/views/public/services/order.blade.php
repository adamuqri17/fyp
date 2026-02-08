@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden mx-auto" style="max-width: 1000px;">
        <div class="row g-0">
            
            <div class="col-lg-4 bg-light border-end d-flex flex-column justify-content-center p-4 text-center position-relative">
                <div class="position-absolute top-0 start-0 p-3">
                    <a href="{{ route('public.services.index') }}" class="text-muted text-decoration-none small fw-bold">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="bg-white rounded-3 p-3 mb-3 shadow-sm mx-auto d-flex align-items-center justify-content-center" style="width: 100%; max-width: 250px; height: 250px;">
                    @if($ledger->picture && str_starts_with($ledger->picture, 'ledgers/'))
                        <img src="{{ asset('storage/' . $ledger->picture) }}" 
                             class="img-fluid" 
                             style="max-height: 100%; object-fit: contain;">
                    @else
                        <i class="fas fa-monument fa-4x text-muted opacity-25"></i>
                    @endif
                </div>
                
                <h5 class="fw-bold text-dark mb-1">{{ $ledger->name }}</h5>
                <p class="text-muted small mb-2">{{ $ledger->material }}</p>
                <h3 class="text-success fw-bold">RM {{ number_format($ledger->price, 0) }}</h3>
            </div>

            <div class="col-lg-8 bg-white p-4 p-md-5">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Order Details</h5>
                        <p class="text-muted small mb-0">Complete the form below to place your order.</p>
                    </div>
                </div>

                <form action="{{ route('public.ledgers.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="ledger_id" value="{{ $ledger->ledger_id }}">
                    <input type="hidden" name="amount" value="{{ $ledger->price }}">

                    <div class="mb-4 position-relative">
                        <label class="form-label text-uppercase small fw-bold text-muted ls-1">Find Deceased / Plot ID <span class="text-danger">*</span></label>
                        
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                            <input type="text" id="smartSearch" class="form-control border-start-0 ps-0" 
                                   placeholder="Type Name (e.g. Ahmad) OR ID (e.g. 105)..." 
                                   autocomplete="off">
                        </div>
                        
                        <div id="searchResults" class="list-group position-absolute w-100 shadow-lg mt-1 rounded-3 overflow-auto" 
                             style="z-index: 1050; display: none; max-height: 250px;"></div>
                    </div>

                    <div id="selectedGraveCard" class="mb-4 p-3 bg-success bg-opacity-10 rounded-3 border border-success border-opacity-25" style="display: none;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-white p-2 rounded text-success me-3 shadow-sm">
                                    <i class="fas fa-map-marker-alt fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0" id="displayDeceasedName"></h6>
                                    <small class="text-muted" id="displayDeathDate"></small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success">Plot <span id="displayGraveId"></span></span>
                                <button type="button" class="btn btn-sm btn-link text-danger text-decoration-none d-block small p-0 mt-1" onclick="clearSelection()">
                                    Change
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="grave_id" id="hidden_grave_id" required>
                    </div>
                    
                    @error('grave_id') 
                        <div class="alert alert-danger py-2 small mb-3"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div> 
                    @enderror

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-uppercase small fw-bold text-muted ls-1">Your Name <span class="text-danger">*</span></label>
                            <input type="text" name="buyer_name" class="form-control" required value="{{ old('buyer_name') }}" placeholder="Full Name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-uppercase small fw-bold text-muted ls-1">WhatsApp No. <span class="text-danger">*</span></label>
                            <input type="text" name="buyer_phone" class="form-control" required placeholder="012-3456789" value="{{ old('buyer_phone') }}">
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success shadow-sm fw-bold py-2 rounded-3" id="submitBtn" disabled>
                            Confirm Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .form-control:focus { box-shadow: none; border-color: #198754; }
    .input-group-text { border-color: #ced4da; }
    .input-group:focus-within .input-group-text { border-color: #198754; }
</style>

<script>
    const searchInput = document.getElementById('smartSearch');
    const resultsBox = document.getElementById('searchResults');
    const selectionCard = document.getElementById('selectedGraveCard');
    const hiddenInput = document.getElementById('hidden_grave_id');
    const submitBtn = document.getElementById('submitBtn');

    // Display Elements
    const dispName = document.getElementById('displayDeceasedName');
    const dispDate = document.getElementById('displayDeathDate');
    const dispId = document.getElementById('displayGraveId');

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        if (query.length < 1) {
            resultsBox.style.display = 'none';
            return;
        }

        fetch(`{{ route('public.services.search') }}?query=${query}`)
            .then(response => response.json())
            .then(data => {
                resultsBox.innerHTML = '';
                if (data.length > 0) {
                    resultsBox.style.display = 'block';
                    data.forEach(item => {
                        const div = document.createElement('button');
                        div.type = 'button';
                        div.className = 'list-group-item list-group-item-action text-start p-2 border-bottom';
                        div.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold text-dark d-block">${item.full_name}</span>
                                    <small class="text-muted" style="font-size: 0.75rem;">Passed: ${item.date_of_death}</small>
                                </div>
                                <span class="badge bg-light text-dark border">ID: ${item.grave_id}</span>
                            </div>
                        `;
                        div.onclick = () => selectGrave(item);
                        resultsBox.appendChild(div);
                    });
                } else {
                    resultsBox.innerHTML = `
                        <div class="p-3 text-center text-muted small">
                            No matches found for "${query}"
                        </div>
                    `;
                    resultsBox.style.display = 'block';
                }
            });
    });

    function selectGrave(item) {
        hiddenInput.value = item.grave_id;
        dispName.innerText = item.full_name;
        dispDate.innerText = item.date_of_death;
        dispId.innerText = item.grave_id;

        resultsBox.style.display = 'none';
        searchInput.value = ''; 
        searchInput.parentElement.style.display = 'none'; 
        selectionCard.style.display = 'block'; 
        submitBtn.disabled = false;
    }

    function clearSelection() {
        hiddenInput.value = '';
        selectionCard.style.display = 'none';
        searchInput.parentElement.style.display = 'flex'; 
        searchInput.focus();
        submitBtn.disabled = true;
    }

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
            resultsBox.style.display = 'none';
        }
    });
</script>
@endsection