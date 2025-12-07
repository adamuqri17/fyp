@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Register New Grave Plot</h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Section Name</label>
                                <select class="form-select">
                                    <option>Select Section</option>
                                    <option>Section A</option>
                                    <option>Section B</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select">
                                    <option value="available">Available</option>
                                    <option value="occupied">Occupied</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pin Location on Map</label>
                            <div style="height: 300px; background-color: #e9ecef; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <span class="text-muted">ArcGIS Map will load here for coordinate picking</span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Latitude</label>
                                <input type="text" class="form-control" placeholder="3.0738...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Longitude</label>
                                <input type="text" class="form-control" placeholder="101.5183...">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success">Save Grave Plot</button>
                            <a href="/admin/dashboard" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection