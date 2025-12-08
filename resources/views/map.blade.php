@extends('layouts.app')

@section('content')
<div class="container-fluid py-3 bg-white shadow-sm mb-3"> <div class="container">
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

<div class="container map-wrapper pb-4"> <div class="card border-0 shadow-lg overflow-hidden">
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
    // 1. Initialize Map
    var map = L.map('map').setView([2.964, 101.508], 19);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 23,
        maxNativeZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // 2. Load the Layout Boundary (The Blue Line)
    fetch('/maps/cemetery_layout1.geojson')
        .then(response => response.json())
        .then(data => {
            L.geoJSON(data, {
                style: { color: 'blue', weight: 2, fillOpacity: 0.1 }
            }).addTo(map);
        });

    // 3. LOAD THE REAL GRAVES (from QGIS)
    fetch('/maps/graves_real.geojson')
        .then(response => response.json())
        .then(data => {
            data.features.forEach(feature => {
                // Get the coordinates of the point you clicked
                var lat = feature.geometry.coordinates[1];
                var lng = feature.geometry.coordinates[0];

                // Create the grave shape at that exact spot
                // We rotate it 25 degrees to match the Qibla/Road orientation
                var corners = getRotatedRect(lat, lng, 1.2, 2.4, 25);

                // Determine color (You can later use feature.properties.status from QGIS)
                var color = '#28a745'; // Default Green
                
                L.polygon(corners, {
                    color: '#555',
                    weight: 1,
                    fillColor: color,
                    fillOpacity: 0.8
                }).bindPopup("<b>Grave ID:</b> " + (feature.properties.id || "Unknown"))
                  .addTo(map);
            });
        })
        .catch(err => console.error("Could not load graves:", err));

    // Helper Math for Rectangles
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