<!DOCTYPE  html>
<?php require_once __DIR__ . '/config.php'; ?>
<html lang="en">    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pic2Map - Not Found</title>
        <link rel="stylesheet" href="<?= APP_ROOT ?>/css/main.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

        <link rel="apple-touch-icon" sizes="180x180" href="<?= APP_ROOT ?>/img/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="<?= APP_ROOT ?>/img/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="<?= APP_ROOT ?>/img/favicon/favicon-16x16.png">
        <link rel="manifest" href="<?= APP_ROOT ?>/img/favicon/site.webmanifest">
    </head>
<body>
    <div id="map"></div>

    <div class="overlay d-flex flex-column">
            <header class="bg-light p-3 d-flex justify-content-between align-items-center shadow-sm">
                <div class="logo-container d-flex align-items-center">
                    <a href="<?= APP_ROOT ?>/">
						<img src="<?= APP_ROOT ?>/img/logo.png" alt="Logo" class="logo" width="70" height="70">
					</a>
                </div>
                <nav>
                    <a href="<?= APP_ROOT ?>/random" class="btn btn-outline-danger me-2">Random</a>
                    <a href="<?= APP_ROOT ?>/" class="btn btn-outline-primary me-2">Home</a>
                    <a href="<?= APP_ROOT ?>/contacts" class="btn btn-outline-secondary">Contact</a>
                </nav>
            </header>

		<main class="main-content">
			<div class="container h-min bg-white m-5">
                <div class="d-flex flex-column gap-5 w-100 h-100 justify-content-center align-items-center">
                    <h1>Oh no! Page not found.</h1>
                    <p>It seems the page you are looking for does not exist.</p>
                    <a href="<?= APP_ROOT ?>/" class="btn btn-outline-primary">Go to Home</a>
			</div>
		</main>

        <footer class="bg-light text-center py-3 shadow-sm">
            &copy; <script>document.write(new Date().getFullYear())</script> Pic2Map Fake.
			<a href="<?= APP_ROOT ?>/tc">Terms and Conditions</a>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const map = L.map('map').setView([0, 0], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    map.setView([lat, lng], 16);
                },
                (err) => {
                    console.warn("Geolocation failed or was denied.");
                    map.setView(["42.674349403151", "23.330461580825677"], 16);
                }
            );
        } else {
            console.warn("Geolocation not supported by this browser.");
        }
    </script>
</body>
</html>