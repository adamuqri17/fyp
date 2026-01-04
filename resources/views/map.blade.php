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
            
            <div id="map" style="height: 500px; width: 100%; z-index: 1;"></div>
            
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
    // We create a "Lookup Dictionary" so the map can quickly find status by ID
    
    const dbGraves = @json($graves); // Data passed from Laravel Controller
    const graveStatus = {};    // Stores: { 5: 'occupied', 6: 'available' }
    const graveInfo = {};      // Stores: { 5: 'Ahmad Albab', 6: 'Book Now' }

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

    // Variable to store layout for the "Focus" button
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
            
            // Optional: Zoom to layout only if not searching
            if (!window.location.search.includes('focus')) {
                map.fitBounds(layoutLayer.getBounds());
            }
        })
        .catch(err => console.log("Layout file missing, skipping."));

    // =========================================================
    // 4. LOAD & COLOR REAL GRAVES (The "Hybrid" Logic)
    // =========================================================
    const graveLayers = {}; // To store references for search zooming

    fetch('/maps/graves_real.geojson')
        .then(response => response.json())
        .then(data => {
            data.features.forEach(feature => {
                // A. GET DATA FROM QGIS FILE
                // IMPORTANT: Your QGIS points must have an 'id' property!
                // If they don't, we can't link them to the database.
                var qgisID = feature.properties.fid; 
                var lat = feature.geometry.coordinates[1];
                var lng = feature.geometry.coordinates[0];

                // B. LOOK UP STATUS IN DATABASE
                // If ID exists in DB, use that status. If not, default to 'available'.
                var status = graveStatus[qgisID] || 'available'; 
                var popupInfo = graveInfo[qgisID] || '<b>Available</b>';

                // C. DETERMINE COLOR
                var color = '#28a745'; // Green
                if (status === 'occupied') color = '#dc3545'; // Red
                else if (status === 'reserved') color = '#ffc107'; // Yellow

                // D. DRAW THE SHAPE
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

                // E. SAVE REFERENCE (For Search Function)
                if (qgisID) {
                    graveLayers[qgisID] = polygon;
                }
            });

            // F. CHECK FOR SEARCH "FOCUS"
            // If user came from Search page (?focus=5), zoom to that grave
            const urlParams = new URLSearchParams(window.location.search);
            const focusId = urlParams.get('focus');

            if (focusId && graveLayers[focusId]) {
                const target = graveLayers[focusId];
                map.setView(target.getBounds().getCenter(), 22); // Zoom in close
                target.openPopup();
                
                // Flash effect
                target.setStyle({ color: 'blue', weight: 3 });
                setTimeout(() => { target.setStyle({ color: '#555', weight: 1 }); }, 3000);
            }
        })
        .catch(err => console.error("Could not load graves:", err));

    // =========================================================
    // 5. HELPER FUNCTIONS
    // =========================================================
    
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
@endsection