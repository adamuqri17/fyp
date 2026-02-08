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
                    <a href="{{ route('admin.graves.create') }}" class="btn btn-primary btn-sm me-2 shadow-sm">
                        <i class="fas fa-plus-circle me-1"></i> Add New Plot
                    </a>
                @endauth
                
                <button class="btn btn-outline-secondary btn-sm me-2" onclick="location.reload()">
                    <i class="fas fa-sync me-1"></i> Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container map-wrapper pb-4"> 
    <div class="card border-0 shadow-lg overflow-hidden">
        <div class="card-body p-0 position-relative">
            
            <div class="position-absolute top-0 start-0 m-3 p-2" style="z-index: 1000; width: 300px;">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-success"></i></span>
                    <input type="text" id="mapSearch" class="form-control border-start-0" placeholder="Search Name or Plot ID..." autocomplete="off">
                </div>
                <div id="mapSearchResults" class="list-group mt-1 shadow-sm" style="display: none; max-height: 300px; overflow-y: auto;"></div>
            </div>

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
                {{-- <div class="d-flex align-items-center mb-0">
                    <div style="width: 15px; height: 15px; background-color: #ffc107; margin-right: 10px; border-radius: 2px;"></div>
                    <span class="small fw-bold text-dark">Reserved</span>
                </div> --}}
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // =========================================================
    // 1. DATA PREPARATION
    // =========================================================
    const dbGraves = @json($graves); 
    const dbGraveMap = {}; 
    const drawnIDs = new Set();
    const isAdmin = @json(Auth::guard('admin')->check());

    // Build Index for fast lookup
    dbGraves.forEach(g => {
        dbGraveMap[g.grave_id] = g;
    });

    // =========================================================
    // 2. INITIALIZE MAP
    // =========================================================
    // Moved zoom controls to top-right so they don't block search bar
    var map = L.map('map', { zoomControl: false }).setView([2.964, 101.508], 19);
    L.control.zoom({ position: 'topright' }).addTo(map);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 23,
        maxNativeZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Load Boundary
    fetch('/maps/cemetery_layout1.geojson')
        .then(response => response.json())
        .then(data => {
            L.geoJSON(data, { style: { color: 'blue', weight: 2, fillOpacity: 0.05 } }).addTo(map);
        });

    const graveLayers = {}; // To store Leaflet layers for search zooming

    // =========================================================
    // 3. LOAD & MERGE GRAVES
    // =========================================================
    fetch('/maps/map2.geojson')
        .then(response => response.json())
        .then(data => {
            data.features.forEach(feature => {
                var qgisID = String(feature.properties.id || feature.properties.fid);
                var lat = feature.geometry.coordinates[1];
                var lng = feature.geometry.coordinates[0];

                var status = 'available';
                var graveData = null;

                // Check if this plot exists in DB
                if (dbGraveMap[qgisID]) {
                    graveData = dbGraveMap[qgisID];
                    status = graveData.status;
                    drawnIDs.add(String(graveData.grave_id));
                }

                drawGrave(lat, lng, status, qgisID, graveData);
            });

            // Draw remaining manually added graves from DB
            dbGraves.forEach(g => {
                if (!drawnIDs.has(String(g.grave_id)) && g.latitude && g.longitude) {
                    drawGrave(g.latitude, g.longitude, g.status, g.grave_id, g);
                }
            });

            // Handle URL ?focus=ID
            const urlParams = new URLSearchParams(window.location.search);
            const focusId = urlParams.get('focus');
            if (focusId) zoomToGrave(focusId);
        });

    // =========================================================
    // 4. DRAW FUNCTION
    // =========================================================
    function drawGrave(lat, lng, status, id, graveData) {
        let color = '#28a745'; // Green
        let popupContent = '';

        // --- POPUP CONTENT LOGIC ---
        if (status === 'occupied') {
            color = '#dc3545';
            popupContent = `<span class="text-danger fw-bold">OCCUPIED</span><br>${graveData?.deceased ? graveData.deceased.full_name : 'Unknown'}`;
        } else if (status === 'reserved') {
            color = '#ffc107';
            popupContent = '<span class="text-warning fw-bold">RESERVED</span>';
        } else {
            // AVAILABLE
            popupContent = '<b>Available</b>';
            
            // ADMIN: Show Register Deceased
            if (isAdmin && graveData) {
                popupContent += `<br><a href="/admin/deceased/create?grave_id=${graveData.grave_id}" class="btn btn-sm btn-success text-white mt-1 w-100"><i class="fas fa-user-plus me-1"></i> Register Deceased</a>`;
            }
            // PUBLIC: No button (View Only)
        }

        // ADMIN: Edit Plot Button (Always visible if grave exists in DB)
        if (isAdmin && graveData) {
            popupContent += `<hr class="my-1"><a href="/admin/graves/${graveData.grave_id}/edit" class="btn btn-sm btn-primary w-100"><i class="fas fa-edit"></i> Edit Plot</a>`;
        }

        var corners = getRotatedRect(parseFloat(lat), parseFloat(lng), 1.2, 2.4, 25);
        
        var polygon = L.polygon(corners, {
            color: '#555', weight: 1, fillColor: color, fillOpacity: 0.8
        }).bindPopup(`
            <div class="text-center">
                <b>Plot ID: ${id}</b><br>
                ${popupContent}
            </div>
        `).addTo(map);

        // Store reference for Search
        if (id) graveLayers[id] = polygon;
    }

    // =========================================================
    // 5. SEARCH BAR LOGIC
    // =========================================================
    const searchInput = document.getElementById('mapSearch');
    const searchResults = document.getElementById('mapSearchResults');

    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        searchResults.innerHTML = ''; // Clear results
        searchResults.style.display = 'none';

        if(query.length < 1) return;

        // Filter Graves (Search by ID or Deceased Name)
        const matches = dbGraves.filter(g => {
            const idMatch = String(g.grave_id).includes(query);
            const nameMatch = g.deceased && g.deceased.full_name.toLowerCase().includes(query);
            return idMatch || nameMatch;
        }).slice(0, 10); // Limit to 10 results

        if(matches.length > 0) {
            searchResults.style.display = 'block';
            matches.forEach(g => {
                const btn = document.createElement('button');
                btn.className = 'list-group-item list-group-item-action text-start';
                
                let desc = `<span class="badge bg-success">Plot ${g.grave_id}</span>`;
                if(g.status === 'occupied' && g.deceased) {
                    desc += ` <span class="fw-bold ms-1">${g.deceased.full_name}</span>`;
                } else if(g.status === 'reserved') {
                    desc += ` <span class="text-warning fw-bold ms-1">Reserved</span>`;
                } else {
                    desc += ` <span class="text-success ms-1">Available</span>`;
                }

                btn.innerHTML = desc;
                btn.onclick = () => {
                    zoomToGrave(g.grave_id);
                    searchResults.style.display = 'none';
                    searchInput.value = '';
                };
                searchResults.appendChild(btn);
            });
        } else {
            searchResults.style.display = 'block';
            searchResults.innerHTML = '<div class="list-group-item text-muted small">No matches found</div>';
        }
    });

    function zoomToGrave(id) {
        const target = graveLayers[id];
        if (target) {
            map.flyTo(target.getBounds().getCenter(), 22, { duration: 1.5 });
            target.openPopup();
            
            // Flash Effect
            target.setStyle({ color: 'blue', weight: 4, fillOpacity: 1 });
            setTimeout(() => { target.setStyle({ color: '#555', weight: 1, fillOpacity: 0.8 }); }, 3000);
        } else {
            alert('Grave ID ' + id + ' is not plotted on the map yet.');
        }
    }

    // =========================================================
    // 6. HELPERS
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
</script>
@endsection