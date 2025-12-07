@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 bg-white shadow-sm mb-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold text-success mb-1">Cemetery Digital Mapping</h2>
                <p class="text-muted mb-0 small">Geospatial Plotting System &bull; Kampung Johan Setia</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <button class="btn btn-success btn-sm shadow-sm" onclick="resetMapView()">
                    <i class="fas fa-crosshairs me-1"></i> Reset View
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container map-wrapper pb-5">
    <div class="map-container position-relative">
        <div id="viewDiv" style="height: 100%; width: 100%; z-index: 1;"></div>
        
        <div class="map-legend" style="z-index: 1000;">
            <h6 class="fw-bold mb-3 small text-uppercase text-secondary" style="letter-spacing: 1px;">Plot Status</h6>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #28a745;"></div>
                <span>Available</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #dc3545;"></div>
                <span>Occupied</span>
            </div>
            <div class="legend-item mb-0">
                <div class="legend-color" style="background-color: #ffc107;"></div>
                <span>Reserved</span>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    // 1. Initialize Map
    // Coordinates for Kampung Johan Setia (Approximated from your PDF location)
    const defaultCenter = [2.9738, 101.4883];
    const defaultZoom = 20; // Very close zoom to see individual graves

    const map = L.map('viewDiv').setView(defaultCenter, defaultZoom);

    // 2. Add OpenStreetMap Tile Layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 22,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    /* --- OPTIONAL: OVERLAY YOUR PDF MAP IMAGE ---
       If you convert your PDF page 1 to an image (layout.png), uncomment this:
       
       const imageUrl = '/images/layout.png';
       const imageBounds = [[2.9750, 101.4880], [2.9720, 101.4900]]; // You must find exact GPS corners
       L.imageOverlay(imageUrl, imageBounds).addTo(map);
    */

    // 3. Helper: Calculate Rotated Rectangle Corners
    // This allows us to angle the graves to face Qibla
    function getRotatedRect(lat, lng, widthMeters, heightMeters, angleDegrees) {
        const center = map.project([lat, lng], map.getMaxZoom());
        
        // Convert meters to pixels (approximate at equator for simplicity, or use geolib)
        // At zoom 22, 1 pixel is tiny. This is a visual approximation.
        // Better approach: working with small degree offsets.
        const earthCircumference = 40075017;
        const latDegreesPerMeter = 360 / earthCircumference;
        const lngDegreesPerMeter = 360 / (earthCircumference * Math.cos(lat * Math.PI / 180));

        const heightDeg = heightMeters * latDegreesPerMeter;
        const widthDeg = widthMeters * lngDegreesPerMeter;

        const angleRad = angleDegrees * (Math.PI / 180);
        
        // Calculate the 4 corners relative to center
        // We use simple rotation formula
        const corners = [
            {x: -widthDeg/2, y: -heightDeg/2},
            {x:  widthDeg/2, y: -heightDeg/2},
            {x:  widthDeg/2, y:  heightDeg/2},
            {x: -widthDeg/2, y:  heightDeg/2}
        ];

        const rotatedCorners = corners.map(p => {
            return [
                lat + (p.x * Math.sin(angleRad) + p.y * Math.cos(angleRad)), // Rotated Latitude
                lng + (p.x * Math.cos(angleRad) - p.y * Math.sin(angleRad))  // Rotated Longitude
            ];
        });

        return rotatedCorners;
    }

    function createPlot(lat, long, color, popupContent) {
        // Grave Size: 1.2m x 2.4m (Standard Layout)
        // Angle: 22 degrees (Perpendicular to Qibla 292)
        const corners = getRotatedRect(lat, long, 1.2, 2.4, 22);

        return L.polygon(corners, {
            color: "#555",       // Border color
            weight: 1,           // Border width
            fillColor: color,    // Status color
            fillOpacity: 0.8
        }).bindPopup(popupContent).addTo(map);
    }

    // 4. GENERATE "LOT A" GRID
    // Instead of random points, let's generate a Block/Section like in your PDF
    const graveData = [];
    
    // Config for generating rows
    const startLat = 2.9738;
    const startLng = 101.4883;
    const rows = 4;
    const cols = 8;
    const gap = 0.000025; // Gap between graves

    let count = 1;
    for (let r = 0; r < rows; r++) {
        for (let c = 0; c < cols; c++) {
            // Logic to make some occupied, some available
            let status = 'available';
            let name = null;
            let date = null;

            // Randomly occupy some
            if (Math.random() > 0.6) {
                status = 'occupied';
                name = 'Deceased #' + count;
                date = '01-01-2024';
            } else if (Math.random() > 0.9) {
                status = 'reserved';
            }

            graveData.push({
                id: `A-${r+1}-${c+1}`,
                lat: startLat - (r * gap),     // Move down for rows
                long: startLng + (c * gap),    // Move right for cols
                status: status,
                name: name,
                date: date
            });
            count++;
        }
    }

    // 5. Draw the Plots
    graveData.forEach(grave => {
        let color = "#28a745"; // Green
        let popupContent = "";

        if (grave.status === 'occupied') {
            color = "#dc3545"; // Red
            popupContent = `
                <div style="font-family: sans-serif; min-width: 150px;">
                    <b style="color: #dc3545;">Occupied Plot</b><br>
                    <hr style="margin: 5px 0;">
                    <b>ID:</b> ${grave.id}<br>
                    <b>Name:</b> ${grave.name}<br>
                    <b>Date:</b> ${grave.date}
                </div>
            `;
        } else if (grave.status === 'reserved') {
            color = "#ffc107"; // Yellow
            popupContent = `
                <div style="font-family: sans-serif; min-width: 150px;">
                    <b style="color: #d39e00;">Reserved Plot</b><br>
                    <hr style="margin: 5px 0;">
                    <b>ID:</b> ${grave.id}
                </div>
            `;
        } else {
            popupContent = `
                <div style="font-family: sans-serif; min-width: 150px;">
                    <b style="color: #28a745;">Available Plot</b><br>
                    <hr style="margin: 5px 0;">
                    <b>ID:</b> ${grave.id}<br>
                    <a href="/contact" class="btn btn-sm btn-success mt-2 text-white">Contact to Reserve</a>
                </div>
            `;
        }

        createPlot(grave.lat, grave.long, color, popupContent);
    });

    function resetMapView() {
        map.setView(defaultCenter, defaultZoom);
    }
</script>
@endsection