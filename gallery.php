<!DOCTYPE  html>
<?php define('APP_ROOT', '/pic2map'); ?>
<html lang="en">    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pic2Map</title>
        <link rel="stylesheet" href="<?= APP_ROOT ?>/css/main.css">
        <link rel="stylesheet" href="<?= APP_ROOT ?>/css/gallery.css">
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
            <div class="container my-2 p-2 bg-white">
                <div class="row h-100">
                    <div class="col-8 h-100">
                        <div class="h-100 w-100" id="map2"></div>    
                    </div>
                    <div class="col overflow-auto">
                        <div class="d-flex flex-row justify-content-between">
                            <h2 class="text-center">Uploaded images</h2>
                            <label for="photoInput" class="btn btn-primary m-2">Upload photos</label>
                            <input type="file" id="photoInput" accept="image/jpeg, image/png, image/webp, image/tiff" multiple hidden>
                        </div>
                        <div class="gallery-container">
                            <ul class="gallery-list list-group" id="gallery-list">
                                <!-- Gallery items will be dynamically inserted here -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="bg-light text-center py-3 shadow-sm">
            &copy; <script>document.write(new Date().getFullYear())</script> Pic2Map Fake.
			<a href="<?= APP_ROOT ?>/tc">Terms and Conditions</a>
        </footer>
    </div>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
        <div id="myToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ✅ Your photo was uploaded successfully!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const APP_ROOT = "<?= APP_ROOT ?>";
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
    </script>
    <script src="<?= APP_ROOT ?>/js/toast.js"></script>
    <script src="<?= APP_ROOT ?>/js/gallery.js"></script>
    <script src="<?= APP_ROOT ?>/js/upload.js"></script>
</body>
</html>