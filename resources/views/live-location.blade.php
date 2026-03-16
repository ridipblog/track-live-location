<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Live Location tracking</title>
    @vite(['resources/js/app.js'])
</head>

<body>
    <div id="map"></div>
    <p id="address"></p>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
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
                    var iframe = $("<iframe>", {
                        width: "100%",
                        height: "400",
                        src: `https://maps.google.com/maps?q=${e.latitude},${e.longitude}&z=15&output=embed`
                    });

                    $("#map").html(iframe);
                });
        });
    </script>

</body>

</html>
