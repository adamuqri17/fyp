@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
                
                <div class="card-header bg-success bg-gradient text-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold"><i class="fas fa-user-plus me-2"></i>Register New Deceased</h5>
                        <p class="mb-0 small text-white-50" style="font-size: 0.85rem;">Enter details and allocate a burial plot.</p>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-light text-success fw-bold shadow-sm">
                        <i class="fas fa-times me-1"></i> Close
                    </a>
                </div>
                
                <div class="card-body p-4 bg-white">
                    <form action="{{ route('admin.deceased.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <h6 class="text-success fw-bold text-uppercase small letter-spacing-1 border-bottom pb-2 mb-3" style="font-size: 0.8rem;">
                                <i class="fas fa-address-card me-2"></i>Personal Information
                            </h6>
                            
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="form-floating">
                                        <input type="text" name="full_name" class="form-control bg-light-input input-sm" id="fullName" placeholder="Full Name" required>
                                        <label for="fullName">Full Name of Deceased <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select name="gender" class="form-select bg-light-input input-sm" id="genderSelect">
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                        <label for="genderSelect">Gender</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" name="ic_number" class="form-control bg-light-input input-sm" id="icNumber" placeholder="IC Number" required>
                                        <label for="icNumber">IC Number / ID <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" name="dob" class="form-control bg-light-input input-sm" id="dob" required>
                                        <label for="dob">Date of Birth</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-success fw-bold text-uppercase small letter-spacing-1 border-bottom pb-2 mb-3" style="font-size: 0.8rem;">
                                <i class="fas fa-calendar-alt me-2"></i>Burial Details
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted mb-1" style="font-size: 0.8rem;">Date of Death</label>
                                    <input type="date" name="dod" class="form-control bg-light-input input-sm" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-success mb-1" style="font-size: 0.8rem;">Burial Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-white border-success py-1"><i class="fas fa-procedures"></i></span>
                                        <input type="date" name="burial_date" class="form-control border-success fw-bold bg-success-subtle input-sm" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-success fw-bold text-uppercase small letter-spacing-1 border-bottom pb-2 mb-3" style="font-size: 0.8rem;">
                                <i class="fas fa-map-marker-alt me-2"></i>Plot Allocation
                            </h6>
                            
                            <div class="p-3 rounded-3 border border-1 shadow-sm" style="background-color: #f8fcf9;">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted mb-1" style="font-size: 0.8rem;">1. Select Section</label>
                                        <select id="sectionSelect" class="form-select shadow-sm border-0 input-sm">
                                            <option value="">-- Choose Section --</option>
                                            @foreach($sections as $section)
                                                <option value="{{ $section->section_id }}" 
                                                    {{ (isset($preselectedSectionId) && $preselectedSectionId == $section->section_id) ? 'selected' : '' }}>
                                                    {{ $section->section_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted mb-1" style="font-size: 0.8rem;">2. Select Available Plot</label>
                                        <div class="shadow-sm rounded bg-white">
                                            <select name="grave_id" id="plotSelect" class="form-select input-sm" required>
                                                
                                                {{-- PHP LOGIC: Pre-fill if coming from Map --}}
                                                @if(isset($preselectedPlots) && count($preselectedPlots) > 0)
                                                    <option value="">-- Select Plot ID --</option>
                                                    @foreach($preselectedPlots as $plot)
                                                        <option value="{{ $plot->grave_id }}" 
                                                            {{ (isset($preselectedGraveId) && $preselectedGraveId == $plot->grave_id) ? 'selected' : '' }}>
                                                            Plot ID: {{ $plot->grave_id }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option value="">Select Section First</option>
                                                @endif

                                            </select>
                                        </div>
                                        <div id="plotLoading" class="text-success small mt-1 ms-1 fw-bold" style="display:none; font-size: 0.75rem;">
                                            <i class="fas fa-spinner fa-spin me-1"></i> Checking availability...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-floating">
                                <textarea name="notes" class="form-control bg-light-input input-sm" id="notes" style="height: 100px" placeholder="Remarks"></textarea>
                                <label for="notes">Additional Notes / Remarks (Optional)</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary px-4 rounded-pill" style="font-size: 0.9rem;">Cancel</a>
                            <button type="submit" class="btn btn-success px-5 shadow-lg rounded-pill fw-bold hover-scale" style="font-size: 0.9rem;">
                                <i class="fas fa-save me-2"></i> Save Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // 1. Initialize Select2
    var $plotSelect = $('#plotSelect');
    $plotSelect.select2({
        theme: 'bootstrap-5',
        placeholder: "Select Section First",
        allowClear: true,
        width: '100%' 
    });

    // 2. Handle Section Change (The Manual Flow)
    $('#sectionSelect').on('change', function() {
        var sectionId = $(this).val();
        loadPlots(sectionId);
    });

    // 3. Reusable Function to Load Plots
    function loadPlots(sectionId) {
        var $loader = $('#plotLoading');

        // Reset and show loading state
        $plotSelect.empty().append('<option value="">Loading...</option>').trigger('change');
        
        if (!sectionId) {
            $plotSelect.empty().append('<option value="">Select Section First</option>').trigger('change');
            return;
        }

        $loader.show();

        $.ajax({
            url: "{{ route('admin.deceased.get-plots') }}",
            type: "GET",
            data: { section_id: sectionId },
            success: function(response) {
                $loader.hide();
                
                var options = '<option value="">-- Select Plot ID --</option>';
                
                if (response.length > 0) {
                    response.forEach(function(plot) {
                        options += '<option value="' + plot.grave_id + '">Plot ID: ' + plot.grave_id + '</option>';
                    });
                } else {
                    options = '<option value="">No Available Plots</option>';
                }

                // Update Select2
                $plotSelect.html(options).trigger('change'); 
            },
            error: function() {
                $loader.hide();
                alert('Error loading plots. Please check your internet connection.');
                $plotSelect.empty().append('<option value="">Error Loading Data</option>').trigger('change');
            }
        });
    }
});
</script>

<style>
    /* Uniform Font Size */
    .input-sm, .form-control, .form-select, .form-label, .btn {
        font-size: 0.9rem !important; /* Slightly smaller than default 1rem */
    }
    
    .form-floating > label {
        font-size: 0.9rem;
    }

    /* Custom Styling */
    .bg-light-input {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
    }
    .bg-light-input:focus {
        background-color: #fff;
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.15);
    }
    .letter-spacing-1 { letter-spacing: 1px; }
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: scale(1.02); }
    
    /* Select2 Tweaks to match standard input height */
    .select2-container .select2-selection--single {
        height: 38px !important; /* Standard Bootstrap Input Height */
        display: flex;
        align-items: center;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        font-size: 0.9rem;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        color: #212529;
        font-weight: 400;
        line-height: 1.5;
    }
</style>
@endsection