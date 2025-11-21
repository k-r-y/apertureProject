<?php
// myPhotos.php - User page to view their uploaded photos

require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';
require_once '../includes/functions/function.php';
require_once '../includes/functions/auth.php';

// Check if user is logged in
if (!isset($_SESSION["userId"]) || !isset($_SESSION["role"]) || $_SESSION["role"] !== "User") {
    header("Location: ../logIn.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Photos - Aperture</title>
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">

    <!-- PhotoSwipe CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/photoswipe@5.3.7/dist/photoswipe.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Old+Standard+TT:wght@400;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>

    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="header-title m-0">My Photos</h1>
                    <span class="badge bg-gold text-dark" id="photoCount">0 Photos</span>
                </div>

                <!-- Loading State -->
                <div class="loading-state text-center py-5" id="loadingState">
                    <div class="spinner-border text-gold" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-secondary mt-3">Loading your photos...</p>
                </div>

                <!-- Empty State -->
                <div class="empty-state text-center py-5 d-none" id="emptyState">
                    <i class="bi bi-images fs-1 text-secondary mb-3 d-block"></i>
                    <h3 class="text-light mb-2">No Photos Yet</h3>
                    <p class="text-secondary">Your photos will appear here once they're uploaded by our team.</p>
                </div>

                <!-- Photo Grid -->
                <div class="photo-grid d-none" id="photoGrid"></div>
            </div>
        </main>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="user.js"></script>

    <!-- PhotoSwipe JS -->
    <script src="https://cdn.jsdelivr.net/npm/photoswipe@5.3.7/dist/photoswipe.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/photoswipe@5.3.7/dist/photoswipe-lightbox.min.js"></script>

    <script>
        const photoGrid = document.getElementById('photoGrid');
        const loadingState = document.getElementById('loadingState');
        const emptyState = document.getElementById('emptyState');
        const photoCount = document.getElementById('photoCount');

        let photos = [];

        // Fetch photos
        async function loadPhotos() {
            try {
                const response = await fetch('getPhotos.php');
                const data = await response.json();

                if (data.success) {
                    photos = data.photos;
                    displayPhotos();
                } else {
                    console.error('Failed to load photos:', data.message);
                    showEmptyState();
                }
            } catch (error) {
                console.error('Error loading photos:', error);
                showEmptyState();
            }
        }

        function displayPhotos() {
            loadingState.classList.add('d-none');

            if (photos.length === 0) {
                showEmptyState();
                return;
            }

            emptyState.classList.add('d-none');
            photoGrid.classList.remove('d-none');
            photoCount.textContent = `${photos.length} Photo${photos.length !== 1 ? 's' : ''}`;

            photoGrid.innerHTML = photos.map((photo, index) => {
                const uploadDate = new Date(photo.uploadDate).toLocaleDateString();
                return `
                    <div class="photo-item">
                        <a href="${photo.url}" 
                           data-pswp-width="1200" 
                           data-pswp-height="800"
                           data-pswp-caption="${photo.caption || photo.originalName}"
                           target="_blank">
                            <img src="${photo.url}" alt="${photo.caption || photo.originalName}" loading="lazy">
                        </a>
                        <div class="photo-overlay">
                            <div class="photo-caption">${photo.caption || ''}</div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="photo-date"><i class="bi bi-calendar3 me-1"></i>${uploadDate}</span>
                                <a href="${photo.url}" download="${photo.originalName}" class="text-white" title="Download">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            // Initialize PhotoSwipe
            initPhotoSwipe();
        }

        function showEmptyState() {
            loadingState.classList.add('d-none');
            photoGrid.classList.add('d-none');
            emptyState.classList.remove('d-none');
            photoCount.textContent = '0 Photos';
        }

        function initPhotoSwipe() {
            const lightbox = new PhotoSwipeLightbox({
                gallery: '#photoGrid',
                children: 'a',
                pswpModule: PhotoSwipe,
                bgOpacity: 0.9,
                padding: { top: 50, bottom: 50, left: 50, right: 50 },
                zoom: true,
                close: true,
                arrowKeys: true,
                returnFocus: false,
                clickToCloseNonZoomable: false,
                imageClickAction: 'zoom',
                tapAction: 'zoom',
                doubleTapAction: 'zoom',
                pinchToClose: true,
                closeOnVerticalDrag: true,
                escKey: true,
                arrowPrev: true,
                arrowNext: true,
                zoom: true,
                counter: true,
                closeTitle: 'Close (Esc)',
                zoomTitle: 'Zoom in/out',
                arrowPrevTitle: 'Previous',
                arrowNextTitle: 'Next',
            });

            lightbox.on('uiRegister', function() {
                lightbox.pswp.ui.registerElement({
                    name: 'download-button',
                    order: 8,
                    isButton: true,
                    tagName: 'a',
                    html: '<i class="bi bi-download"></i>',
                    onInit: (el, pswp) => {
                        el.setAttribute('download', '');
                        el.setAttribute('target', '_blank');
                        el.setAttribute('rel', 'noopener');
                        el.style.cssText = 'color: white; font-size: 1.2rem; padding: 0.5rem;';

                        pswp.on('change', () => {
                            const currentSlide = pswp.currSlide;
                            el.href = currentSlide.data.src;
                        });
                    }
                });
            });

            lightbox.init();
        }

        // Load photos on page load
        loadPhotos();
    </script>
</body>
</html>
