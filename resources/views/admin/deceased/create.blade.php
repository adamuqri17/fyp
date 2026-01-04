@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Register Deceased Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.deceased.store') }}" method="POST">
                        @csrf

                        <h6 class="text-muted mb-3">Personal Information</h6>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">IC Number</label>
                                <input type="text" name="ic_number" class="form-control" placeholder="e.g 900101-10-5555" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="dob" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date of Death</label>
                                <input type="date" name="dod" class="form-control" required>
                            </div>
                        </div>

                        <hr>
                        
                        <h6 class="text-muted mb-3">Burial Details</h6>
                        <div class="mb-3">
                            <label class="form-label">Assign to Grave Plot</label>
                            <select name="grave_id" class="form-select" required>
                                <option value="">Select Available Plot...</option>
                                @foreach($availableGraves as $grave)
                                    <option value="{{ $grave->grave_id }}">
                                        {{ $grave->section->section_name ?? 'Unknown Section' }} - ID {{ $grave->grave_id }}
                                    </option>
                                @endforeach
                            </select>
                            @if($availableGraves->isEmpty())
                                <small class="text-danger">No available plots found. Please create a new plot first.</small>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes / Remarks</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Save Record</button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection