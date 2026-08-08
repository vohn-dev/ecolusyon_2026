<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>EcoLusyon — Public Flood-Waste Heatmap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>#map{height:80vh;}</style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="p-3"><h1 class="h5 mb-0">Public Flood-Waste Heatmap</h1></div>
        <div id="map"></div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const map = L.map('map').setView([14.65, 121.0], 12); // NCR-ish center
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        const colors = { minor: '#F5C451', partial_blockage: '#E8843A', full_blockage: '#C0392B' };
        const reports = @json($reports);

        reports.forEach(r => {
            L.circleMarker([r.latitude, r.longitude], {
                radius: 8, color: colors[r.severity], fillColor: colors[r.severity], fillOpacity: 0.7,
            }).bindPopup(`Status: ${r.status}<br>Severity: ${r.severity.replace('_',' ')}`).addTo(map);
        });
    </script>
</body>
</html>
