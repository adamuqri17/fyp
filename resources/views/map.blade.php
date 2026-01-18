@extends('layouts.app')

@section('content')
<div class="container-fluid py-3 bg-white shadow-sm mb-3"> 
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold text-success mb-1">Cemetery Digital Mapping</h2>
                <p class="text-muted mb-0 small">Geospatial Plotting System &bull; Kampung Johan Setia</p>
            </div>
            <div class="col-md-4 text-md-end mt-2 mt-md-0">
                @auth('admin')
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-dark btn-sm me-2">
                        <i class="fas fa-columns me-1"></i> Dashboard
                    </a>
                @endauth
                
                <button class="btn btn-outline-secondary btn-sm me-2" onclick="location.reload()">
                    <i class="fas fa-sync me-1"></i> Refresh
                </button>
                <button class="btn btn-success btn-sm shadow-sm" onclick="zoomToLayout()">
                    <i class="fas fa-crosshairs me-1"></i> Focus Layout
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container map-wrapper pb-4"> 
    <div class="card border-0 shadow-lg overflow-hidden">
        <div class="card-body p-0 position-relative">
            
            <div id="map" style="height: 600px; width: 100%; z-index: 1;"></div>
            
            <div class="position-absolute bg-white p-3 rounded shadow-sm" style="bottom: 20px; right: 20px; z-index: 1000; min-width: 160px; border-left: 5px solid #198754;">
                <h6 class="fw-bold mb-3 small text-uppercase text-secondary" style="letter-spacing: 1px;">Legend</h6>
                <div class="d-flex align-items-center mb-2">
                    <div style="width: 20px; height: 5px; background-color: blue; margin-right: 10px;"></div>
                    <span class="small fw-bold text-dark">Zone Boundary</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <div style="width: 15px; height: 15px; background-color: #28a745; margin-right: 10px; border-radius: 2px;"></div>
                    <span class="small fw-bold text-dark">Available</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <div style="width: 15px; height: 15px; background-color: #dc3545; margin-right: 10px; border-radius: 2px;"></div>
                    <span class="small fw-bold text-dark">Occupied</span>
                </div>
                <div class="d-flex align-items-center mb-0">
                    <div style="width: 15px; height: 15px; background-color: #ffc107; margin-right: 10px; border-radius: 2px;"></div>
                    <span class="small fw-bold text-dark">Reserved</span>
                </div>
            </div>

        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // =========================================================
    // 1. PREPARE DATABASE DATA (The "Brain")
    // =========================================================
    const dbGraves = @json($graves); 
    const graveStatus = {};    
    const graveInfo = {};      

    dbGraves.forEach(g => {
        graveStatus[g.grave_id] = g.status;
        
        if (g.status === 'occupied' && g.deceased) {
            graveInfo[g.grave_id] = `
                <span class="text-danger fw-bold">OCCUPIED</span><br>
                ${g.deceased.full_name}<br>
                <small>Buried: ${g.deceased.burial_date || 'N/A'}</small>
            `;
        } else if (g.status === 'reserved') {
            graveInfo[g.grave_id] = '<span class="text-warning fw-bold">RESERVED</span>';
        } else {
            graveInfo[g.grave_id] = '<a href="/contact" class="btn btn-sm btn-success text-white mt-1">Book Plot</a>';
        }
    });

    // =========================================================
    // 2. INITIALIZE MAP
    // =========================================================
    var map = L.map('map').setView([2.964, 101.508], 19);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 23,
        maxNativeZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    var layoutLayer;

    // =========================================================
    // 3. LOAD BACKGROUND LAYOUT (Blue Line)
    // =========================================================
    fetch('/maps/cemetery_layout1.geojson')
        .then(response => response.json())
        .then(data => {
            layoutLayer = L.geoJSON(data, {
                style: { color: 'blue', weight: 2, fillOpacity: 0.05 }
            }).addTo(map);
            
            if (!window.location.search.includes('focus')) {
                map.fitBounds(layoutLayer.getBounds());
            }
        })
        .catch(err => console.log("Layout file missing, skipping."));

    // =========================================================
    // 4. LOAD & COLOR REAL GRAVES (The "Hybrid" Logic)
    // =========================================================
    const graveLayers = {}; 

    fetch('/maps/map2.geojson')
        .then(response => response.json())
        .then(data => {
            data.features.forEach(feature => {
                
                // --- A. GET DATA FROM QGIS FILE ---
                // We check for 'id' OR 'fid' to prevent errors if QGIS named it differently
                var qgisID = feature.properties.id || feature.properties.fid; 
                
                var lat = feature.geometry.coordinates[1];
                var lng = feature.geometry.coordinates[0];

                // --- B. LOOK UP STATUS IN DATABASE ---
                var status = graveStatus[qgisID] || 'available'; 
                var popupInfo = graveInfo[qgisID] || '<b>Available</b>';

                // --- C. DETERMINE COLOR ---
                var color = '#28a745'; // Green
                if (status === 'occupied') color = '#dc3545'; // Red
                else if (status === 'reserved') color = '#ffc107'; // Yellow

                // --- D. DRAW THE SHAPE (Point -> Rectangle) ---
                // 1.2m Width, 2.4m Height, 25 degree rotation
                var corners = getRotatedRect(lat, lng, 1.2, 2.4, 25);

                var polygon = L.polygon(corners, {
                    color: '#555',
                    weight: 1,
                    fillColor: color,
                    fillOpacity: 0.8
                }).bindPopup(`
                    <div class="text-center">
                        <b>Plot ID: ${qgisID || '?'}</b><br>
                        ${popupInfo}
                    </div>
                `).addTo(map);

                // --- E. SAVE REFERENCE ---
                if (qgisID) {
                    graveLayers[qgisID] = polygon;
                    
                    // Add permanent label (White Text)
                    polygon.bindTooltip(String(qgisID), {
                        permanent: true,
                        direction: 'center',
                        className: 'grave-label'
                    });
                }
            });

            // --- F. CHECK FOR SEARCH "FOCUS" ---
            const urlParams = new URLSearchParams(window.location.search);
            const focusId = urlParams.get('focus');

            if (focusId && graveLayers[focusId]) {
                const target = graveLayers[focusId];
                map.setView(target.getBounds().getCenter(), 22); 
                target.openPopup();
                
                target.setStyle({ color: 'blue', weight: 3 });
                setTimeout(() => { target.setStyle({ color: '#555', weight: 1 }); }, 3000);
            }
        })
        .catch(err => console.error("Could not load graves:", err));

    // =========================================================
    // 5. HELPER FUNCTIONS
    // =========================================================
    
    // This math converts a single dot (lat/lng) into a rotated rectangle box
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

    function zoomToLayout() {
        if(layoutLayer) map.fitBounds(layoutLayer.getBounds());
    }
</script>

<style>
    /* Style for the White Numbers */
    .grave-label {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        color: white !important;
        font-weight: 900;
        font-size: 10px;
        text-align: center;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
    }
</style>
@endsection