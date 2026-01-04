@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edit Deceased Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.deceased.update', $deceased->deceased_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $deceased->full_name) }}" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">IC Number</label>
                                <input type="text" name="ic_number" class="form-control" value="{{ old('ic_number', $deceased->ic_number) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="Male" {{ $deceased->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ $deceased->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="dob" class="form-control" value="{{ old('dob', $deceased->date_of_birth) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date of Death</label>
                                <input type="date" name="dod" class="form-control" value="{{ old('dod', $deceased->date_of_death) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $deceased->notes) }}</textarea>
                        </div>

                        <div class="alert alert-light border">
                            <strong>Current Grave:</strong> 
                            Section {{ $deceased->grave->section->section_name ?? 'Gen' }} - Plot {{ $deceased->grave->grave_id }}
                            <br>
                            <small class="text-muted">To move this person to a different grave, please delete this record and register it again.</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Update Record</button>
                            <a href="{{ route('admin.deceased.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection