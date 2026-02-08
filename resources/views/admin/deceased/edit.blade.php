@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
                
                <div class="card-header bg-success bg-gradient text-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold"><i class="fas fa-user-edit me-2"></i>Edit Deceased Information</h5>
                        <p class="mb-0 small text-white-50" style="font-size: 0.85rem;">Update personal details and records.</p>
                    </div>
                    <a href="{{ route('admin.deceased.index') }}" class="btn btn-sm btn-light text-success fw-bold shadow-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
                
                <div class="card-body p-4 bg-white">
                    <form id="updateForm" action="{{ route('admin.deceased.update', $deceased->deceased_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <h6 class="text-success fw-bold text-uppercase small letter-spacing-1 border-bottom pb-2 mb-3" style="font-size: 0.8rem;">
                                <i class="fas fa-address-card me-2"></i>Personal Information
                            </h6>
                            
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="form-floating">
                                        <input type="text" name="full_name" class="form-control bg-light-input input-sm" id="fullName" 
                                               value="{{ old('full_name', $deceased->full_name) }}" required>
                                        <label for="fullName">Full Name</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select name="gender" class="form-select bg-light-input input-sm" id="genderSelect">
                                            <option value="Male" {{ $deceased->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ $deceased->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                        <label for="genderSelect">Gender</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" name="ic_number" class="form-control bg-light-input input-sm" id="icNumber" 
                                               value="{{ old('ic_number', $deceased->ic_number) }}" required>
                                        <label for="icNumber">IC Number / ID</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" name="date_of_birth" class="form-control bg-light-input input-sm" id="dob" 
                                               value="{{ old('date_of_birth', $deceased->date_of_birth) }}" required>
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
                                    <div class="form-floating">
                                        <input type="date" name="date_of_death" class="form-control bg-light-input input-sm" id="dod" 
                                               value="{{ old('date_of_death', $deceased->date_of_death) }}" required>
                                        <label for="dod">Date of Death</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" name="burial_date" class="form-control border-success bg-success-subtle input-sm fw-bold" id="burialDate" 
                                               value="{{ old('burial_date', $deceased->burial_date) }}" required>
                                        <label for="burialDate" class="text-success">Burial Date</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-success fw-bold text-uppercase small letter-spacing-1 border-bottom pb-2 mb-3" style="font-size: 0.8rem;">
                                <i class="fas fa-map-marker-alt me-2"></i>Current Plot Location
                            </h6>
                            
                            <div class="alert alert-light border shadow-sm d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="badge bg-success mb-1">Active Plot</span>
                                    <h6 class="mb-1 text-dark fw-bold">
                                        Section {{ $deceased->grave->section->section_name ?? 'Gen' }} &bull; 
                                        Plot ID: {{ $deceased->grave->grave_id }}
                                    </h6>
                                    <small class="text-muted" style="font-size: 0.8rem;">
                                        To relocate this deceased, please delete this record and register again at the new plot location.
                                    </small>
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('admin.map.manager', ['focus' => $deceased->grave->grave_id]) }}" class="btn btn-outline-success btn-sm rounded-pill">
                                        <i class="fas fa-map me-1"></i> View on Map
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-floating">
                                <textarea name="notes" class="form-control bg-light-input input-sm" id="notes" style="height: 100px">{{ old('notes', $deceased->notes) }}</textarea>
                                <label for="notes">Additional Notes / Remarks</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('admin.deceased.index') }}" class="btn btn-outline-secondary px-4 rounded-pill" style="font-size: 0.9rem;">Cancel</a>
                            <button type="button" class="btn btn-success px-5 shadow-lg rounded-pill fw-bold hover-scale" data-bs-toggle="modal" data-bs-target="#confirmationModal" style="font-size: 0.9rem;">
                                <i class="fas fa-save me-2"></i> Update Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-check fa-3x"></i>
                    </div>
                </div>
                
                <h4 class="fw-bold mb-2">Confirm Changes</h4>
                <p class="text-muted mb-4" style="font-size: 0.9rem;">
                    Are you sure you want to update the information for <br>
                    <strong class="text-dark">{{ $deceased->full_name }}</strong>?
                </p>
                
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success fw-bold py-2 shadow-sm rounded-pill" onclick="document.getElementById('updateForm').submit()">
                        Yes, Update Record
                    </button>
                    <button type="button" class="btn btn-light fw-bold py-2 text-muted rounded-pill" data-bs-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Uniform Font Size */
    .input-sm, .form-control, .form-select, .form-label, .btn {
        font-size: 0.9rem !important; 
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
</style>
@endsection