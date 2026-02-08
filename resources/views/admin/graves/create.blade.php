@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="row h-100">
        <div class="col-lg-8 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-success mb-0"><i class="fas fa-plus-circle me-2"></i>Plot New Grave</h5>
                        <small class="text-muted">Click to plot, then <b>DRAG the Blue Pin</b> to adjust.</small>
                    </div>
                </div>
                <div class="card-body p-0 position-relative">
                    <div id="pickerMap" style="height: 650px; width: 100%;"></div>
                    
                    <div id="overlapAlert" class="position-absolute top-0 start-50 translate-middle-x mt-3" style="display: none; z-index: 2000;">
                        <div class="alert alert-danger fw-bold shadow">
                            <i class="fas fa-exclamation-triangle me-2"></i> Overlap Detected!
                        </div>
                    </div>

                    <div class="position-absolute bg-white p-3 rounded shadow-sm" style="bottom: 20px; right: 20px; z-index: 1000; border-left: 5px solid #198754;">
                        <div class="small mb-1"><span style="color:#28a745">■</span> Available (Standard)</div>
                        <div class="small mb-1"><span style="color:#dc3545">■</span> Occupied (Database)</div>
                        <div class="small fw-bold"><span style="color:#0d6efd">■</span> New Selection (Draggable)</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card shadow-lg border-0" style="max-height: 800px; overflow-y: auto;">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Registration Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.graves.store') }}" method="POST" id="createForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Section <span class="text-danger">*</span></label>
                            <select name="section_id" class="form-select" required>
                                <option value="">-- Select Section --</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->section_id }}">{{ $section->section_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <select name="status" id="statusSelect" class="form-select" required onchange="toggleDeceasedForm()">
                                <option value="available" selected>Available</option>
                                <option value="occupied">Occupied</option>
                                <option value="reserved">Reserved</option>
                            </select>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="small text-muted">Latitude</label>
                                <input type="text" name="latitude" id="lat" class="form-control fw-bold" required oninput="onManualInput()">
                            </div>
                            <div class="col-6">
                                <label class="small text-muted">Longitude</label>
                                <input type="text" name="longitude" id="lng" class="form-control fw-bold" required oninput="onManualInput()">
                            </div>
                        </div>

                        <div id="deceasedForm" style="display: none; border-top: 2px dashed #ccc; padding-top: 20px;">
                            <h6 class="text-danger fw-bold mb-3">Deceased Information</h6>
                            
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">IC Number</label>
                                <input type="text" name="ic_number" class="form-control" placeholder="e.g. 901010-10-1234">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="small">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control">
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="small">Date of Death</label>
                                    <input type="date" name="date_of_death" class="form-control">
                                </div>
                                <div class="col-6">
                                    <label class="small">Burial Date</label>
                                    <input type="date" name="burial_date" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" id="submitBtn" class="btn btn-success shadow-sm" disabled>
                                <i class="fas fa-save me-2"></i> Save Record
                            </button>
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
    // 1. SETUP MAP
    const map = L.map('pickerMap').setView([2.964, 101.508], 19); 
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 22, attribution: '© OpenStreetMap' }).addTo(map);

    const dbGraves = @json($existingGraves);
    const dbGraveIDs = new Set();
    const allObstacles = []; 
    
    // VARIABLES FOR PLOTTING
    let plotPolygon = null;
    let plotHandle = null; // The Draggable Marker

    // =========================================================
    // 2. LOAD DATA
    // =========================================================

    // A. DB Graves
    dbGraves.forEach(g => {
        if(g.latitude && g.longitude) {
            dbGraveIDs.add(String(g.grave_id)); 
            let color = g.status === 'occupied' ? '#dc3545' : (g.status === 'reserved' ? '#ffc107' : '#28a745');
            const corners = getRotatedRect(parseFloat(g.latitude), parseFloat(g.longitude), 1.2, 2.4, 25);
            allObstacles.push(corners);

            L.polygon(corners, {
                color: '#555', weight: 1, fillColor: color, fillOpacity: 0.8, interactive: false
            }).bindPopup(`
                <div class="text-center">
                    <b>ID: ${g.grave_id}</b><br>
                    Status: ${g.status.toUpperCase()}<br>
                    <a href="/admin/graves/${g.grave_id}/edit" class="btn btn-sm btn-warning mt-2 text-dark fw-bold">Edit</a>
                </div>
             `).addTo(map);
        }
    });

    // B. GeoJSON Graves
    fetch('/maps/map2.geojson')
        .then(res => res.json())
        .then(data => {
            data.features.forEach(feature => {
                var qgisID = String(feature.properties.id || feature.properties.fid);
                if (!dbGraveIDs.has(qgisID)) {
                    var lat = feature.geometry.coordinates[1];
                    var lng = feature.geometry.coordinates[0];
                    const corners = getRotatedRect(lat, lng, 1.2, 2.4, 25);
                    allObstacles.push(corners);

                    L.polygon(corners, {
                        color: '#555', weight: 1, fillColor: '#28a745', fillOpacity: 0.4, interactive: false
                    }).addTo(map);
                }
            });
        });

    // C. Boundary
    fetch('/maps/cemetery_layout1.geojson')
        .then(res => res.json())
        .then(d => L.geoJSON(d, { style: { color: 'blue', weight: 2, fillOpacity: 0.05, dashArray: '5, 5' } }).addTo(map));


    // =========================================================
    // 3. INTERACTIVE PLOTTING (Draggable Logic)
    // =========================================================
    
    // Click Map to start plotting
    map.on('click', function(e) {
        updatePlot(e.latlng.lat, e.latlng.lng);
    });

    // Manual Typing
    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');

    function onManualInput() {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);
        if(!isNaN(lat) && !isNaN(lng)) {
            // Update map WITHOUT updating inputs (to prevent cursor jumping)
            updateVisuals(lat, lng);
        }
    }

    // MAIN UPDATE FUNCTION
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
        if (plotPolygon) plotPolygon.setLatLngs(corners);
        else plotPolygon = L.polygon(corners, { color: '#0d6efd', weight: 2, fillColor: '#0d6efd', fillOpacity: 0.4 }).addTo(map);

        // B. Draw/Move Marker (The Handle)
        if (plotHandle) {
            plotHandle.setLatLng([lat, lng]);
        } else {
            plotHandle = L.marker([lat, lng], { draggable: true }).addTo(map);
            
            // DRAG EVENT LISTENER
            plotHandle.on('drag', function(e) {
                const newLat = e.target.getLatLng().lat;
                const newLng = e.target.getLatLng().lng;
                updatePlot(newLat, newLng); // Update polygon and inputs while dragging
            });
        }

        // C. Check Collision
        if (checkCollision(corners)) {
            document.getElementById('overlapAlert').style.display = 'block';
            document.getElementById('submitBtn').disabled = true;
            plotPolygon.setStyle({ color: '#dc3545', fillColor: '#dc3545' }); // Red
        } else {
            document.getElementById('overlapAlert').style.display = 'none';
            document.getElementById('submitBtn').disabled = false;
            plotPolygon.setStyle({ color: '#0d6efd', fillColor: '#0d6efd' }); // Blue
        }
    }


    // =========================================================
    // 4. HELPERS
    // =========================================================
    function toggleDeceasedForm() {
        const val = document.getElementById('statusSelect').value;
        const form = document.getElementById('deceasedForm');
        form.style.display = (val === 'occupied') ? 'block' : 'none';
        
        form.querySelectorAll('input, select').forEach(i => {
            if (val === 'occupied') {
                if(!i.name.includes('date_of_birth') && !i.name.includes('burial_date')) i.required = true;
            } else {
                i.required = false;
            }
        });
    }

    function checkCollision(newCorners) {
        for (let i = 0; i < allObstacles.length; i++) {
            if (polygonsIntersect(newCorners, allObstacles[i])) return true;
        }
        return false;
    }

    function polygonsIntersect(a, b) { for (let pt of a) if (isPointInPoly(pt, b)) return true; for (let pt of b) if (isPointInPoly(pt, a)) return true; return false; }
    function isPointInPoly(pt, poly) { var x=pt[0],y=pt[1]; var inside=false; for(var i=0,j=poly.length-1;i<poly.length;j=i++){ var xi=poly[i][0],yi=poly[i][1]; var xj=poly[j][0],yj=poly[j][1]; var intersect=((yi>y)!=(yj>y))&&(x<(xj-xi)*(y-yi)/(yj-yi)+xi); if(intersect) inside=!inside; } return inside; }
    function getRotatedRect(lat, lng, w, h, a) { const metersPerLat=111320; const metersPerLng=40075000*Math.cos(lat*Math.PI/180)/360; const dy=h/2/metersPerLat; const dx=w/2/metersPerLng; const ar=a*(Math.PI/180); const corners=[{x:-dx,y:-dy},{x:dx,y:-dy},{x:dx,y:dy},{x:-dx,y:dy}]; return corners.map(p=>[lat+(p.x*Math.sin(ar)+p.y*Math.cos(ar)), lng+(p.x*Math.cos(ar)-p.y*Math.sin(ar))]); }
</script>
@endsection