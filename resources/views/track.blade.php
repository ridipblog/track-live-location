<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Track Location</title>
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
    <h1>Track Location</h1>
    <p>Latitude: <span id="latitude"></span></p>
    <p>Longitude: <span id="longitude"></span></p>
    <p>Address: <span id="address"></span></p>
    <button id="update-cordinations">Update sCordinates</button>
    <div id="map"></div>

    <div id="message"></div>
    <button id="send-location">Send Location</button>

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
                    console.log("Event received!", e.message);
                });
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition);
            } else {
                alert("Geolocation is not supported by this browser.");
            }

            function showPosition(position, extra = null) {
                console.log("position", position);
                $.ajax({
                    url: "/update-location",
                    method: "POST",
                    data: {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        console.log(response);
                    }
                });

                document.getElementById("latitude").innerHTML = position.coords.latitude;
                document.getElementById("longitude").innerHTML = position.coords.longitude;
                fetch(
                        `https://nominatim.openstreetmap.org/reverse?lat=${position.coords.latitude}&lon=${position.coords.longitude}&format=json`
                    )
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById("address").innerHTML = data.display_name;
                    });
                // var iframe = $("<iframe>", {
                //     width: "100%",
                //     height: "400",
                //     src: `https://maps.google.com/maps?q=${position.coords.latitude},${position.coords.longitude}&z=15&output=embed`
                // });

                // $("#map").html(iframe);

                // Move map
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
                path.push([lat, lng]);

                // Draw line
                if (polyline) {
                    polyline.setLatLngs(path);
                } else {
                    polyline = L.polyline(path, {
                        color: 'blue',
                        weight: 5
                    }).addTo(map);
                }
            }

            $("#update-cordinations").click(function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showPosition);
                }
            });

            $("#send-location").click(function() {
                $.ajax({
                    url: "/update-location",
                    method: "POST",
                    data: {
                        latitude: "26.156180268016268",
                        longitude: "91.77840797839819",
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        alert("Location sent successfully");
                    }

                });
                alert("Location sent successfully");
            });
        });
    </script>
</body>

</html>
