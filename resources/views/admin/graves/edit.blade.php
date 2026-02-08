@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="row h-100">
        <div class="col-lg-8 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-warning text-dark py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0"><i class="fas fa-edit me-2"></i>Relocating Grave ID: {{ $grave->grave_id }}</h5>
                        <small class="text-dark"><b>DRAG</b> the Blue Pin to the correct location.</small>
                    </div>
                </div>
                <div class="card-body p-0 position-relative">
                    <div id="editMap" style="height: 650px; width: 100%;"></div>
                    
                    <div id="overlapAlert" class="position-absolute top-0 start-50 translate-middle-x mt-3" style="display: none; z-index: 2000;">
                        <div class="alert alert-danger fw-bold shadow">
                            <i class="fas fa-exclamation-triangle me-2"></i> Overlap Detected!
                        </div>
                    </div>

                    <div class="position-absolute bg-white p-3 rounded shadow-sm" style="bottom: 20px; right: 20px; z-index: 1000; border-left: 5px solid #ffc107;">
                        <div class="small mb-1"><span style="color:#0d6efd">■</span> Target Grave (Editable)</div>
                        <div class="small mb-1"><span style="color:#dc3545">■</span> Occupied</div>
                        <div class="small mb-1"><span style="color:#ffc107">■</span> Reserved</div>
                        <div class="small mb-1"><span style="color:#28a745">■</span> Available</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card shadow-lg border-0" style="max-height: 800px; overflow-y: auto;">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Update Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.graves.update', $grave->grave_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <h6 class="text-warning fw-bold mb-3">Location</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Section</label>
                            <select name="section_id" class="form-select">
                                @foreach($sections as $section)
                                    <option value="{{ $section->section_id }}" {{ $grave->section_id == $section->section_id ? 'selected' : '' }}>
                                        {{ $section->section_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="small text-muted">Latitude</label>
                                <input type="text" name="latitude" id="lat" class="form-control fw-bold" value="{{ $grave->latitude }}" required oninput="onManualInput()">
                            </div>
                            <div class="col-6">
                                <label class="small text-muted">Longitude</label>
                                <input type="text" name="longitude" id="lng" class="form-control fw-bold" value="{{ $grave->longitude }}" required oninput="onManualInput()">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" id="statusSelect" class="form-select" onchange="toggleDeceasedForm()">
                                <option value="available" {{ $grave->status == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="occupied" {{ $grave->status == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                <option value="reserved" {{ $grave->status == 'reserved' ? 'selected' : '' }}>Reserved</option>
                            </select>
                        </div>

                        <div id="deceasedForm" style="display: {{ $grave->status == 'occupied' ? 'block' : 'none' }}; border-top: 2px dashed #ccc; padding-top: 20px;">
                            <h6 class="text-danger fw-bold mb-3">Deceased Information</h6>
                            <div class="mb-2">
                                <label class="small">Full Name</label>
                                <input type="text" name="full_name" class="form-control" value="{{ $grave->deceased->full_name ?? '' }}">
                            </div>
                            <div class="mb-2">
                                <label class="small">IC Number</label>
                                <input type="text" name="ic_number" class="form-control" value="{{ $grave->deceased->ic_number ?? '' }}">
                            </div>
                            <div class="mb-2">
                                <label class="small">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="Male" {{ ($grave->deceased->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ ($grave->deceased->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6"><small>Death Date</small><input type="date" name="date_of_death" class="form-control" value="{{ $grave->deceased->date_of_death ?? '' }}"></div>
                                <div class="col-6"><small>Burial Date</small><input type="date" name="burial_date" class="form-control" value="{{ $grave->deceased->burial_date ?? '' }}"></div>
                            </div>
                        </div>

                        <div class="d-grid mt-4 gap-2">
                            <button type="submit" id="submitBtn" class="btn btn-warning shadow-sm">
                                <i class="fas fa-save me-2"></i> Update Changes
                            </button>
                            <a href="{{ route('admin.map.manager') }}" class="btn btn-outline-secondary">Cancel</a>
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
    // 1. DATA SETUP
    const currentGrave = @json($grave);
    const otherGraves = @json($otherGraves); // Passed from controller
    const allObstacles = [];

    // Form Elements
    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');

    // Plotting Variables
    let plotPolygon = null;
    let plotHandle = null; // The Draggable Marker

    // 2. INITIALIZE MAP
    const map = L.map('editMap').setView([currentGrave.latitude, currentGrave.longitude], 21);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 23,
        maxNativeZoom: 19
    }).addTo(map);

    // 3. LOAD STATIC LAYERS (Boundary & Other Graves)
    
    // A. Boundary
    fetch('/maps/cemetery_layout1.geojson')
        .then(res => res.json())
        .then(data => {
            L.geoJSON(data, { style: { color: 'blue', weight: 2, fillOpacity: 0.05 }, interactive: false }).addTo(map);
        });

    // B. Other Graves (Obstacles)
    otherGraves.forEach(g => {
        let color = '#28a745';
        if (g.status === 'occupied') color = '#dc3545';
        if (g.status === 'reserved') color = '#ffc107';

        const corners = getRotatedRect(parseFloat(g.latitude), parseFloat(g.longitude), 1.2, 2.4, 25);
        
        // Add to collision list
        allObstacles.push(corners);

        // Draw on map
        L.polygon(corners, {
            color: '#555', weight: 1, fillColor: color, fillOpacity: 0.5, interactive: false
        }).addTo(map);
    });

    // 4. INITIALIZE THE EDITABLE GRAVE
    // We call updatePlot once immediately to draw the marker and polygon at the current location
    updatePlot(parseFloat(currentGrave.latitude), parseFloat(currentGrave.longitude));

    // 5. MANUAL INPUT HANDLER
    function onManualInput() {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);
        if(!isNaN(lat) && !isNaN(lng)) {
            updateVisuals(lat, lng);
        }
    }

    // 6. CORE FUNCTIONS (Copied from Create.blade.php logic)
    
    // Updates Inputs AND Visuals
    function updatePlot(lat, lng) {
        // 1. Update Inputs
        latInput.value = lat.toFixed(8);
        lngInput.value = lng.toFixed(8);
        
        // 2. Update Map Visuals
        updateVisuals(lat, lng);
    }

    function updateVisuals(lat, lng) {
        const corners = getRotatedRect(lat, lng, 1.2, 2.4, 25);
        
        // A. Draw/Move Polygon
        if (plotPolygon) {
            plotPolygon.setLatLngs(corners);
        } else {
            plotPolygon = L.polygon(corners, { 
                color: '#0d6efd', weight: 2, fillColor: '#0d6efd', fillOpacity: 0.4 
            }).addTo(map);
        }

        // B. Draw/Move Marker (The Handle)
        if (plotHandle) {
            plotHandle.setLatLng([lat, lng]);
        } else {
            plotHandle = L.marker([lat, lng], { draggable: true, zIndexOffset: 1000 }).addTo(map);
            
            // CRITICAL: Bind Drag Event Here
            plotHandle.on('drag', function(e) {
                const newLat = e.target.getLatLng().lat;
                const newLng = e.target.getLatLng().lng;
                
                // Recursively call updatePlot to sync inputs while dragging
                updatePlot(newLat, newLng); 
            });
        }

        // C. Check Collision
        if (checkCollision(corners)) {
            document.getElementById('overlapAlert').style.display = 'block';
            document.getElementById('submitBtn').disabled = true; // Prevent saving overlapping grave
            plotPolygon.setStyle({ color: '#dc3545', fillColor: '#dc3545' }); // Red
        } else {
            document.getElementById('overlapAlert').style.display = 'none';
            document.getElementById('submitBtn').disabled = false;
            plotPolygon.setStyle({ color: '#0d6efd', fillColor: '#0d6efd' }); // Blue
        }
    }

    // 7. HELPER FUNCTIONS
    function toggleDeceasedForm() {
        const status = document.getElementById('statusSelect').value;
        const form = document.getElementById('deceasedForm');
        form.style.display = (status === 'occupied') ? 'block' : 'none';
    }

    function checkCollision(newCorners) {
        for (let i = 0; i < allObstacles.length; i++) {
            if (polygonsIntersect(newCorners, allObstacles[i])) return true;
        }
        return false;
    }

    // Geometry Math (Same as create.blade.php)
    function polygonsIntersect(a, b) { 
        for (let pt of a) if (isPointInPoly(pt, b)) return true; 
        for (let pt of b) if (isPointInPoly(pt, a)) return true; 
        return false; 
    }
    
    function isPointInPoly(pt, poly) { 
        var x=pt[0],y=pt[1]; 
        var inside=false; 
        for(var i=0,j=poly.length-1;i<poly.length;j=i++){ 
            var xi=poly[i][0],yi=poly[i][1]; 
            var xj=poly[j][0],yj=poly[j][1]; 
            var intersect=((yi>y)!=(yj>y))&&(x<(xj-xi)*(y-yi)/(yj-yi)+xi); 
            if(intersect) inside=!inside; 
        } 
        return inside; 
    }
    
    function getRotatedRect(lat, lng, widthM, heightM, angle) {
        const metersPerLat = 111320; 
        const metersPerLng = 40075000 * Math.cos(lat * Math.PI / 180) / 360;
        const dy = heightM / 2 / metersPerLat;
        const dx = widthM / 2 / metersPerLng;
        const angleRad = angle * (Math.PI / 180);
        const corners = [{x: -dx, y: -dy}, {x: dx, y: -dy}, {x: dx, y: dy}, {x: -dx, y: dy}];
        return corners.map(p => [
            lat + (p.x * Math.sin(angleRad) + p.y * Math.cos(angleRad)),
            lng + (p.x * Math.cos(angleRad) - p.y * Math.sin(angleRad))
        ]);
    }
</script>
@endsection