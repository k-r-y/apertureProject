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
    <link rel="stylesheet" href="../luxuryDesignSystem.css">
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">

    <!-- PhotoSwipe CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/photoswipe@5.4.3/dist/photoswipe.css" crossorigin="anonymous">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">


    <style>
          /* Additional styles for photo hover effects */
        .photo-card-luxury img:hover {
            transform: scale(1.05);
        }
        .photo-overlay-luxury {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .photo-card-luxury a:hover .photo-overlay-luxury {
            opacity: 1;
        }
        
        /* PhotoSwipe Customization */
        .pswp-luxury .pswp__bg {
            background: rgba(10, 10, 10, 0.95) !important;
            backdrop-filter: blur(10px);
        }
        
        /* Prevent image stretching in PhotoSwipe */
        .pswp img {
            object-fit: contain;
        }
    </style>

</head>

<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>

    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content">
            <div class="container-fluid px-3 px-lg-5 py-5">
                
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div>
                        <h1 class="mb-2">My Gallery</h1>
                        <p class="text-muted">Your captured moments</p>
                    </div>
                    <div class="neo-card px-4 py-2 d-flex align-items-center gap-3">
                        <i class="bi bi-images text-gold fs-4"></i>
                        <div>
                            <div class="text-muted small">Total Photos</div>
                            <div class="h5 m-0 text-light" id="photoCount">0</div>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div class="loading-state text-center py-5" id="loadingState">
                    <div class="spinner-border text-gold" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-3">Curating your gallery...</p>
                </div>

                <!-- Empty State -->
                <div class="empty-state text-center py-5 d-none" id="emptyState">
                    <div class="neo-card d-inline-block p-5">
                        <i class="bi bi-camera fs-1 text-gold mb-4 d-block"></i>
                        <h3 class="text-light mb-3">No Photos Yet</h3>
                        <p class="text-muted mb-0">Your photos will appear here once they're uploaded by our team.</p>
                    </div>
                </div>

                <!-- Photo Grid -->
                <div class="row g-4 d-none" id="photoGrid"></div>
            </div>
        </main>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="user.js"></script>

    <!-- PhotoSwipe JS -->
    <script src="https://cdn.jsdelivr.net/npm/photoswipe@5.4.3/dist/umd/photoswipe.umd.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/photoswipe@5.4.3/dist/umd/photoswipe-lightbox.umd.min.js" crossorigin="anonymous"></script>

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
            photoCount.textContent = photos.length;

            photoGrid.innerHTML = photos.map((photo, index) => {
                const uploadDate = new Date(photo.uploadDate).toLocaleDateString();
                return `
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="neo-card h-100 p-2 photo-card-luxury">
                            <a href="${photo.url}" 
                               class="photo-link d-block position-relative overflow-hidden rounded mb-2"
                               data-pswp-caption="${photo.caption || photo.originalName}">
                                <img src="${photo.url}" alt="${photo.caption || photo.originalName}" loading="lazy" class="img-fluid w-100" style="aspect-ratio: 1/1; object-fit: cover; transition: transform 0.5s ease;">
                                <div class="photo-overlay-luxury">
                                    <i class="bi bi-zoom-in fs-3 text-white"></i>
                                </div>
                            </a>
                            <div class="px-2 pb-2">
                                <div class="text-light fw-medium text-truncate mb-1">${photo.caption || 'Untitled'}</div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>${uploadDate}</small>
                                    <a href="${photo.url}" download="${photo.originalName}" class="btn btn-sm btn-gold rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Download">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
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
            photoCount.textContent = '0';
        }

        function initPhotoSwipe() {
            // Check if PhotoSwipe library is loaded
            if (typeof PhotoSwipeLightbox === 'undefined' || typeof PhotoSwipe === 'undefined') {
                console.error('PhotoSwipe library not loaded');
                return;
            }

            const lightbox = new PhotoSwipeLightbox({
                gallery: '#photoGrid',
                children: 'a.photo-link',
                pswpModule: PhotoSwipe,
                bgOpacity: 0.95,
                padding: { top: 20, bottom: 20, left: 20, right: 20 },
                mainClass: 'pswp-luxury',
                
                // UI options
                zoom: true,
                close: true,
                arrowKeys: true,
                counter: true,
                
                // Animation
                showHideAnimationType: 'zoom',
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
                        el.style.cssText = 'color: var(--gold-main); font-size: 1.5rem; padding: 10px; transition: color 0.3s ease;';
                        
                        el.addEventListener('mouseenter', () => {
                            el.style.color = '#fff';
                        });
                        el.addEventListener('mouseleave', () => {
                            el.style.color = 'var(--gold-main)';
                        });

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

    <style>
      
    </style>
</body>
</html>
