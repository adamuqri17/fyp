<script>
    // 1. Initialize Map
    const map = L.map('adminMap').setView([2.97385, 101.4884], 19); 

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 22,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // 2. DRAW ROADS (Visual Reference based on PDF)
    
    // Road: Lebuh Kebun Nenas 35 (Diagonal Top-Right)
    const roadNenas = [
        [2.97420, 101.48850], // Top Left
        [2.97410, 101.48900], // Top Right
        [2.97350, 101.48920], // Bottom Right
        [2.97360, 101.48870]  // Bottom Left
    ];
    
    L.polygon(roadNenas, {
        color: 'transparent',
        fillColor: '#7f8c8d', // Asphalt Gray
        fillOpacity: 0.3
    }).bindTooltip("Lebuh Kebun Nenas 35", {permanent: true, direction: "center"}).addTo(map);

    // Path: Simpanan Jalan (Center Vertical)
    const centerPath = [
        [2.97410, 101.48850],
        [2.97350, 101.48850]
    ];
    
    L.polyline(centerPath, {
        color: '#95a5a6',
        weight: 10, // Thick line for path
        dashArray: '10, 10', // Dashed line
        opacity: 0.5
    }).bindTooltip("Jalan Utama", {direction: "center"}).addTo(map);


    // 3. Load Graves
    const graves = @json($graves);

    // 4. MATH HELPER: Rotated Graves (Qibla 292° => ~22.6° Rotation)
    function getRotatedRect(centerLat, centerLng) {
        const widthMeters = 1.0; 
        const heightMeters = 2.2; 
        const angleDegrees = 22.6; // Exact Qibla adjustment

        const earthCircumference = 40075017;
        const latDegreesPerMeter = 360 / earthCircumference;
        const lngDegreesPerMeter = 360 / (earthCircumference * Math.cos(centerLat * Math.PI / 180));

        const heightDeg = heightMeters * latDegreesPerMeter;
        const widthDeg = widthMeters * lngDegreesPerMeter;
        const angleRad = angleDegrees * (Math.PI / 180);

        const corners = [
            {x: -widthDeg/2, y: -heightDeg/2},
            {x:  widthDeg/2, y: -heightDeg/2},
            {x:  widthDeg/2, y:  heightDeg/2},
            {x: -widthDeg/2, y:  heightDeg/2} 
        ];

        return corners.map(p => [
            centerLat + (p.x * Math.sin(angleRad) + p.y * Math.cos(angleRad)), 
            centerLng + (p.x * Math.cos(angleRad) - p.y * Math.sin(angleRad))
        ]);
    }

    // 5. Draw Plots
    graves.forEach(grave => {
        let color = '#28a745'; 
        let fillColor = '#28a745';
        
        if(grave.status === 'occupied') { color = '#dc3545'; fillColor = '#dc3545'; } 
        else if(grave.status === 'reserved') { color = '#ffc107'; fillColor = '#ffc107'; }

        const polygonCoords = getRotatedRect(parseFloat(grave.latitude), parseFloat(grave.longitude));

        const poly = L.polygon(polygonCoords, {
            color: '#333',
            weight: 1,
            fillColor: fillColor,
            fillOpacity: 0.65
        }).addTo(map);

        // Click Event (Same as before)
        poly.on('click', function() {
            document.getElementById('emptyState').classList.add('d-none');
            document.getElementById('editorPanel').classList.remove('d-none');
            document.getElementById('displayId').innerText = grave.grave_id;
            document.getElementById('inputStatus').value = grave.status;
            
            const updateUrl = "{{ route('admin.graves.update', ':id') }}".replace(':id', grave.grave_id);
            document.getElementById('editForm').action = updateUrl;

            const infoBox = document.getElementById('deceasedInfo');
            const viewBtn = document.getElementById('viewBtn');
            
            if(grave.deceased) {
                infoBox.innerHTML = `
                    <div class="alert alert-secondary mb-0 p-2">
                        <i class="fas fa-user text-dark"></i> <b>${grave.deceased.full_name}</b><br>
                        <small>IC: ${grave.deceased.ic_number}</small>
                    </div>
                `;
                viewBtn.classList.remove('d-none');
            } else {
                infoBox.innerHTML = "<small class='text-muted'>No burial record linked.</small>";
                viewBtn.classList.add('d-none');
            }
        });
    });
</script>