<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Live Location tracking</title>
    @vite(['resources/js/app.js'])

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
    <p id="address"></p>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let map;
        let marker;
        let path = [];
        let polyline;
        $(document).ready(function() {
            // Initialize Map
            map = L.map('map').setView([0, 0], 2);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);

            window.Echo.channel('location-channel')
                .listen('.SendLocationEvent', (e) => {
                    console.log(e.latitude, e.longitude);
                    fetch(
                            `https://nominatim.openstreetmap.org/reverse?lat=${e.latitude}&lon=${e.longitude}&format=json`
                        )
                        .then(res => res.json())
                        .then(data => {
                            console.log(data.display_name);
                            document.getElementById("address").innerHTML = data.display_name;
                        });
                    // var iframe = $("<iframe>", {
                    //     width: "100%",
                    //     height: "400",
                    //     src: `https://maps.google.com/maps?q=${e.latitude},${e.longitude}&z=15&output=embed`
                    // });

                    // $("#map").html(iframe);

                    map.setView([e.latitude, e.longitude], 15);

                    if (marker) {
                        marker.setLatLng([e.latitude, e.longitude]);
                    } else {
                        marker = L.marker([e.latitude, e.longitude]).addTo(map);
                    }

                    // Save route path
                    path.push([e.latitude, e.longitude]);

                    // Draw line
                    if (polyline) {
                        polyline.setLatLngs(path);
                    } else {
                        polyline = L.polyline(path, {
                            color: 'blue',
                            weight: 5
                        }).addTo(map);
                    }
                });
        });
    </script>

</body>

</html>
