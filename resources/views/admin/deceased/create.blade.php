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
                    <form>
                        <h6 class="text-muted mb-3">Personal Information</h6>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" placeholder="Enter full name">
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">IC Number</label>
                                <input type="text" class="form-control" placeholder="e.g 900101-10-5555">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender</label>
                                <select class="form-select">
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date of Death</label>
                                <input type="date" class="form-control">
                            </div>
                        </div>

                        <hr>
                        <h6 class="text-muted mb-3">Burial Details</h6>
                        <div class="mb-3">
                            <label class="form-label">Assign to Grave Plot</label>
                            <select class="form-select">
                                <option>Select Available Plot...</option>
                                <option>Section A - ID 101 (Available)</option>
                                <option>Section A - ID 102 (Available)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes / Remarks</label>
                            <textarea class="form-control" rows="3"></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary">Save Record</button>
                            <a href="/admin/dashboard" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection