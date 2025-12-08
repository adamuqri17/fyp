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
                    <form action="{{ route('admin.graves.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Section</label>
                                <select name="section_id" class="form-select" required>
                                    <option value="">Select Section...</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->section_id }}">{{ $section->section_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="available">Available</option>
                                    <option value="reserved">Reserved</option>
                                    <option value="occupied">Occupied</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-flex justify-content-between">
                                <span>Pin Location</span>
                                <small class="text-muted fw-normal">Existing plots are shown on the map</small>
                            </label>
                            <div class="d-flex gap-3 mb-2 small">
                                <span><span style="color:#dc3545">■</span> Occupied</span>
                                <span><span style="color:#28a745">■</span> Available</span>
                                <span><span style="color:#ffc107">■</span> Reserved</span>
                                <span><i class="fas fa-map-marker-alt text-primary"></i> <b>New Selection</b></span>
                            </div>
                            
                            <div id="pickerMap" style="height: 400px; border-radius: 8px; border: 1px solid #ced4da;"></div>
                            <small class="text-muted">Click an empty space to place the new grave.</small>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Latitude</label>
                                <input type="text" name="latitude" id="lat" class="form-control bg-light" placeholder="0.000000" readonly required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Longitude</label>
                                <input type="text" name="longitude" id="lng" class="form-control bg-light" placeholder="0.000000" readonly required>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">Save Grave Plot</button>
                            <a href="{{ route('admin.graves.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // 1. Initialize Map
    // Default focus on Johan Setia
    const map = L.map('pickerMap').setView([2.9738, 101.4883], 19);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 22,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // 2. Load Existing Graves (Passed from Controller)
    const existingGraves = @json($existingGraves);

    // Helper: Draw Plot Rectangles (Same logic as Public Map)
    function drawPlot(lat, lng, status) {
        // Plot Size (Approx 1.2m x 2.4m)
        const width = 0.000015; 
        const height = 0.000025; 
        
        let color = "#28a745"; // Default Green
        if (status === 'occupied') color = "#dc3545"; // Red
        else if (status === 'reserved') color = "#ffc107"; // Yellow

        const bounds = [
            [lat - height, lng - width], 
            [lat + height, lng + width]
        ];

        L.rectangle(bounds, {
            color: "#555",
            weight: 1,
            fillColor: color,
            fillOpacity: 0.5, // 50% opacity so they look "background"
            interactive: false // Admin can't click these, only empty space
        }).addTo(map);
    }

    // Render all existing graves
    existingGraves.forEach(grave => {
        // Ensure we have valid coordinates before drawing
        if(grave.latitude && grave.longitude) {
            drawPlot(parseFloat(grave.latitude), parseFloat(grave.longitude), grave.status);
        }
    });

    // 3. Handle "New Selection" Click
    let newMarker;

    map.on('click', function(e) {
        const lat = e.latlng.lat.toFixed(7);
        const lng = e.latlng.lng.toFixed(7);

        // Update Inputs
        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;

        // Remove previous selection marker if exists
        if (newMarker) {
            map.removeLayer(newMarker);
        }

        // Add blue pin for NEW selection
        newMarker = L.marker(e.latlng).addTo(map);
        newMarker.bindPopup("<b>New Location</b>").openPopup();
    });
</script>
@endsection