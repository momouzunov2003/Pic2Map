<!DOCTYPE  html>
<?php require_once __DIR__ . '/config.php'; ?>
<html lang="en">    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pic2Map</title>
        <link rel="stylesheet" href="<?= APP_ROOT ?>/css/main.css">
        <link rel="stylesheet" href="<?= APP_ROOT ?>/css/index.css">
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
                <a href="<?= APP_ROOT ?>/" class="btn btn-primary disabled me-2">Home</a>
                <a href="<?= APP_ROOT ?>/contacts" class="btn btn-outline-secondary">Contact</a>
            </nav>
        </header>

        <main class="main-content">
			<div class="container h-100">
				<h1 class="text-center mt-5">Welcome to Pic2Map</h1>
				<p class="text-center">Discover the world through pictures.</p>
				<div class="h-50 d-flex flex-column justify-content-center align-items-center">
					<h2 class="text-center">How it works</h2>
					<p class="text-center">Upload pictures and we will show you where they were taken on the map.</p>
					<div class="text-center">
                        <button class="btn btn-primary mb-0" onclick="window.location.href='<?= APP_ROOT ?>/create-gallery'">
                            Create Gallery
                        </button>
					</div>
				</div>
			</div>
        </main>

        <footer class="bg-light text-center py-3 shadow-sm">
            &copy; <script>document.write(new Date().getFullYear())</script> Pic2Map Fake.
			<a href="<?= APP_ROOT ?>/tc">Terms and Conditions</a>
        </footer>
    </div>

    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100;"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="<?= APP_ROOT ?>/js/toast.js"></script>
    
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
					console.debug(err)
                    map.setView(["42.674349403151", "23.330461580825677"], 16);
                }
            );
        } else {
            console.warn("Geolocation not supported by this browser.");
        }
       
        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            if (params.get('toast') === 'gallery_deleted') {
                showToast('Gallery deleted successfully.', 'success');
    
                window.history.replaceState({}, document.title, window.location.pathname);
            } else if (params.get('toast') === 'nogalleries') {
                showToast('No galleries to show.', 'warning');
    
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>
</body>
</html>