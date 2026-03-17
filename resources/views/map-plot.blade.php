{{-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Map Plot</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
</head>

<body>
    <div id="map"></div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        let map;
        let marker;
        let path = [];
        let polyline;

        let lastUpdateTime = 0;
        const interval = 1000; // 5 seconds

        // Initialize Map
        map = L.map('map').setView([0, 0], 2);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);

        navigator.geolocation.watchPosition(function(position) {

            const now = Date.now();

            // ⛔ Skip if last update was less than 5 sec ago
            // if (now - lastUpdateTime < interval) {
            //     return;
            // }

            // lastUpdateTime = now;

            let lat = position.coords.latitude;
            let lng = position.coords.longitude;
            console.log(lat, lng);

            map.setView([position.coords.latitude, position.coords.longitude], 15);

            // Add or update marker
            if (marker) {
                marker.setLatLng([position.coords.latitude, position.coords.longitude]);
            } else {
                marker = L.marker([position.coords.latitude, position.coords.longitude]).addTo(map)
                    .bindPopup("You are here")
                    .openPopup();
            }

            // Save route path
            path.push([position.coords.latitude, position.coords.longitude]);

            // Draw line
            if (polyline) {
                polyline.setLatLngs(path);
            } else {
                polyline = L.polyline(path, {
                    color: 'blue',
                    weight: 5
                }).addTo(map);
            }

        }, function(error) {
            console.log(error);
        }, {
            enableHighAccuracy: true,
            maximumAge: 0,
            timeout: 100
        });
    </script>
</body>

</html> --}}


{{-- **** Old code *** --}}




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Live Route Tracking</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <!-- Routing CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />

    <style>
        #map {
            height: 100vh;
        }
    </style>
</head>

<body>

    <div id="map"></div>
    <button onclick="addLocation1()">Click1 </button>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- Routing JS -->
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

    <script>
        // 🗺️ 1. Initialize map
        let map = L.map('map').setView([26.1445, 91.7362], 13); // Assam default

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        // 📍 2. Routing control
        let routeControl = L.Routing.control({
            waypoints: [],
            routeWhileDragging: false,
            addWaypoints: false,
            draggableWaypoints: false,
            fitSelectedRoutes: true,
            show: false
        }).addTo(map);

        // 📍 3. Marker
        let marker = null;

        // 📍 4. Waypoints storage
        let waypoints = [];

        // ⏱ 5. Control update frequency
        let lastUpdateTime = 0;
        const interval = 3000; // 3 seconds

        // 🚫 6. Distance filter (avoid GPS jump)
        let lastPoint = null;

        function isValidPoint(lat, lng) {
            if (!lastPoint) return true;

            let distance = map.distance(
                [lastPoint.lat, lastPoint.lng],
                [lat, lng]
            );

            return distance < 100; // ignore big jumps
        }

        function setMapView(lat, lng) {
            console.log("GPS:", lat, lng);

            // 🚫 skip bad GPS
            if (!isValidPoint(lat, lng)) return;

            lastPoint = {
                lat,
                lng,
                time: Date.now()
            };

            // 📍 Move map
            map.setView([lat, lng], 15);

            // 📍 Marker update
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng]).addTo(map)
                    .bindPopup("You are here")
                    .openPopup();
            }

            // 📍 Add waypoint
            waypoints.push(L.latLng(lat, lng));

            // 🚀 Limit number of points (important)
            if (waypoints.length > 15) {
                waypoints.shift();
            }

            // 🛣️ Update route (road-based)
            routeControl.setWaypoints(waypoints);
        }

        // 📡 7. Start tracking
        navigator.geolocation.watchPosition(function(position) {

            let now = Date.now();

            // ⏱ limit updates
            if (now - lastUpdateTime < interval) return;
            lastUpdateTime = now;

            let lat = position.coords.latitude;
            let lng = position.coords.longitude;

            setMapView(lat, lng);

        }, function(error) {
            console.log("Error:", error.message);
        }, {
            enableHighAccuracy: true,
            maximumAge: 0
        });

        function addLocation1() {
            setMapView("26.128616842300676", "91.73714630788221");
        }
    </script>

</body>

</html>
