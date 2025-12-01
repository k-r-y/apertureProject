<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Gallery - Aperture Studios</title>
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <link rel="stylesheet" href="luxuryDesignSystem.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="assets/camera.png" type="image/x-icon">
    
    <!-- PhotoSwipe -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/5.4.2/photoswipe.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        body { background-color: #000; color: #fff; min-height: 100vh; display: flex; flex-direction: column; }
        .gallery-login { max-width: 400px; margin: auto; padding: 2rem; background: rgba(20, 20, 20, 0.9); border: 1px solid #333; border-radius: 8px; }
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; padding: 2rem; }
        .gallery-item { position: relative; aspect-ratio: 3/2; overflow: hidden; border-radius: 4px; cursor: pointer; transition: transform 0.3s ease; }
        .gallery-item:hover { transform: scale(1.02); }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; }
        .pin-input { letter-spacing: 0.5em; text-align: center; font-size: 1.5rem; }
    </style>
</head>
<body>

    <!-- PIN Entry Screen -->
    <div id="loginScreen" class="d-flex align-items-center justify-content-center flex-grow-1">
        <div class="gallery-login text-center">
            <img src="assets/logo-for-dark.png" alt="Aperture" height="40" class="mb-4">
            <h4 class="serif text-gold mb-3">Private Gallery Access</h4>
            <p class="text-muted mb-4">Please enter the PIN provided to you.</p>
            
            <form id="pinForm">
                <div class="mb-3">
                    <input type="text" id="pinInput" class="form-control bg-dark text-light border-secondary pin-input" maxlength="6" placeholder="••••••" required>
                </div>
                <button type="submit" class="btn btn-gold w-100">View Photos</button>
            </form>
        </div>
    </div>

    <!-- Gallery View -->
    <div id="galleryView" style="display: none;">
        <nav class="navbar navbar-dark bg-black border-bottom border-secondary px-4">
            <div class="d-flex align-items-center">
                <img src="assets/logo-for-dark.png" alt="Aperture" height="30" class="me-3">
                <span class="text-muted border-start border-secondary ps-3" id="galleryTitle">Event Gallery</span>
            </div>
            <button class="btn btn-sm btn-ghost" onclick="location.reload()">Exit</button>
        </nav>

        <div class="gallery-grid" id="photoGrid">
            <!-- Photos will be injected here -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="module">
        import PhotoSwipeLightbox from 'https://cdnjs.cloudflare.com/ajax/libs/photoswipe/5.4.2/photoswipe-lightbox.esm.min.js';
        import PhotoSwipe from 'https://cdnjs.cloudflare.com/ajax/libs/photoswipe/5.4.2/photoswipe.esm.min.js';

        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get('token');

        if (!token) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Link',
                text: 'Invalid gallery link',
                confirmButtonColor: '#D4AF37',
                background: '#1a1a1a',
                color: '#fff'
            }).then(() => {
                window.location.href = 'index.php';
            });
        }

        document.getElementById('pinForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const pin = document.getElementById('pinInput').value;
            
            try {
                const response = await fetch('api/gallery_api.php?action=verify_pin', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({token, pin})
                });
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('loginScreen').style.display = 'none';
                    document.getElementById('galleryView').style.display = 'block';
                    document.getElementById('galleryTitle').textContent = `${data.event_type} - ${new Date(data.event_date).toLocaleDateString()}`;
                    loadPhotos();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Access Denied',
                        text: data.message,
                        confirmButtonColor: '#D4AF37',
                        background: '#1a1a1a',
                        color: '#fff'
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred',
                    confirmButtonColor: '#D4AF37',
                    background: '#1a1a1a',
                    color: '#fff'
                });
            }
        });

        async function loadPhotos() {
            const response = await fetch(`api/gallery_api.php?action=get_photos&token=${token}`);
            const data = await response.json();
            
            if (data.success) {
                const grid = document.getElementById('photoGrid');
                
                // Prepare items for PhotoSwipe
                const items = data.photos.map(photo => ({
                    src: photo.src,
                    width: photo.w,
                    height: photo.h
                }));

                // Render grid
                grid.innerHTML = data.photos.map((photo, index) => `
                    <a href="${photo.src}" 
                       data-pswp-width="${photo.w}" 
                       data-pswp-height="${photo.h}" 
                       target="_blank"
                       class="gallery-item">
                        <img src="${photo.src}" alt="Photo ${index + 1}" loading="lazy">
                    </a>
                `).join('');

                // Initialize PhotoSwipe
                const lightbox = new PhotoSwipeLightbox({
                    gallery: '#photoGrid',
                    children: 'a',
                    pswpModule: PhotoSwipe
                });
                lightbox.init();
            }
        }
    </script>
</body>
</html>
