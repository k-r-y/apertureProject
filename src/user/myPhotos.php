<?php
// myPhotos.php - User's photo gallery

require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';
require_once '../includes/functions/function.php';
require_once '../includes/functions/auth.php';

// Ensure user is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: ../logIn.php");
    exit;
}

$userID = $_SESSION['userId'];

// Fetch user's bookings that are completed or post-production for the filter dropdown
$bookingsStmt = $conn->prepare("
    SELECT bookingID, event_type, event_date, gdrive_link 
    FROM bookings 
    WHERE userID = ? AND (booking_status = 'completed' OR is_fully_paid = 1)
    ORDER BY event_date DESC
");
$bookingsStmt->bind_param("i", $userID);
$bookingsStmt->execute();
$bookingsResult = $bookingsStmt->get_result();
$bookings = [];
while ($row = $bookingsResult->fetch_assoc()) {
    $bookings[] = $row;
}

// Fetch photos with booking info
$sql = "
    SELECT p.*, b.event_type, b.event_date, b.gdrive_link
    FROM user_photos p
    LEFT JOIN bookings b ON p.bookingID = b.bookingID
    WHERE p.userID = ?
    ORDER BY p.uploadDate DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$photos = [];
while ($row = $result->fetch_assoc()) {
    $photos[] = $row;
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
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="../libs/photoswipe/photoswipe.css">
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Old+Standard+TT:wght@400;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .gallery-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            aspect-ratio: 1;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: var(--card-bg);
        }

        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.05);
        }

        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            padding: 1rem;
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }

        .photo-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.6);
            color: var(--gold);
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            border: 1px solid var(--gold);
        }

        .filter-bar {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>

    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="header-title m-0">My Photos</h1>
                </div>

                <!-- Filters -->
                <div class="filter-bar">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <label class="form-label text-muted small text-uppercase">Event</label>
                            <select class="form-select bg-dark text-light border-secondary" id="eventFilter">
                                <option value="all">All Events</option>
                                <?php foreach ($bookings as $booking): ?>
                                    <option value="<?= $booking['bookingID'] ?>" data-link="<?= htmlspecialchars($booking['gdrive_link'] ?? '') ?>">
                                        <?= htmlspecialchars($booking['event_type']) ?> (<?= date('M d, Y', strtotime($booking['event_date'])) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small text-uppercase">Photo Type</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="typeFilter" id="filterAll" value="all" checked>
                                <label class="btn btn-outline-secondary" for="filterAll">All</label>

                                <input type="radio" class="btn-check" name="typeFilter" id="filterEdited" value="edited">
                                <label class="btn btn-outline-gold" for="filterEdited">Edited</label>

                                <input type="radio" class="btn-check" name="typeFilter" id="filterRaw" value="raw">
                                <label class="btn btn-outline-gold" for="filterRaw">Raw</label>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="#" id="gdriveBtn" class="btn btn-gold d-none" target="_blank">
                                <i class="bi bi-google me-2"></i>View Full Gallery in Drive
                            </a>
                        </div>
                    </div>
                </div>

                <?php if (empty($photos)): ?>
                    <div class="text-center py-5">
                        <div class="display-1 text-muted mb-3"><i class="bi bi-images"></i></div>
                        <h3 class="text-light">No Photos Yet</h3>
                        <p class="text-muted">Your photos will appear here once they are uploaded by the admin.</p>
                    </div>
                <?php else: ?>
                    <div class="gallery-grid" id="galleryGrid">
                        <?php foreach ($photos as $photo): ?>
                            <div class="gallery-item" 
                                 data-booking-id="<?= $photo['bookingID'] ?? '0' ?>"
                                 data-photo-type="<?= $photo['photo_type'] ?? 'edited' ?>">
                                <a href="../uploads/users/<?= $userID ?>/<?= $photo['fileName'] ?>" 
                                   data-pswp-width="1600" 
                                   data-pswp-height="1200" 
                                   target="_blank">
                                    <img src="../uploads/users/<?= $userID ?>/<?= $photo['fileName'] ?>" alt="<?= htmlspecialchars($photo['caption']) ?>" loading="lazy">
                                </a>
                                <div class="photo-badge">
                                    <?= ucfirst($photo['photo_type'] ?? 'edited') ?>
                                </div>
                                <div class="gallery-overlay">
                                    <div class="text-white small">
                                        <?= htmlspecialchars($photo['caption']) ?>
                                    </div>
                                    <a href="../uploads/users/<?= $userID ?>/<?= $photo['fileName'] ?>" download class="btn btn-sm btn-light rounded-circle">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script type="module">
        import PhotoSwipeLightbox from '../libs/photoswipe/photoswipe-lightbox.esm.js';
        import PhotoSwipe from '../libs/photoswipe/photoswipe.esm.js';

        const lightbox = new PhotoSwipeLightbox({
            gallery: '#galleryGrid',
            children: 'a',
            pswpModule: PhotoSwipe
        });
        lightbox.init();

        // Filtering Logic
        const eventFilter = document.getElementById('eventFilter');
        const typeFilters = document.querySelectorAll('input[name="typeFilter"]');
        const galleryItems = document.querySelectorAll('.gallery-item');
        const gdriveBtn = document.getElementById('gdriveBtn');

        function filterPhotos() {
            const selectedEvent = eventFilter.value;
            const selectedType = document.querySelector('input[name="typeFilter"]:checked').value;

            galleryItems.forEach(item => {
                const itemEvent = item.dataset.bookingId;
                const itemType = item.dataset.photoType;

                const eventMatch = selectedEvent === 'all' || itemEvent === selectedEvent;
                const typeMatch = selectedType === 'all' || itemType === selectedType;

                if (eventMatch && typeMatch) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });

            // Update GDrive Button
            if (selectedEvent !== 'all') {
                const selectedOption = eventFilter.options[eventFilter.selectedIndex];
                const link = selectedOption.dataset.link;
                if (link) {
                    gdriveBtn.href = link;
                    gdriveBtn.classList.remove('d-none');
                } else {
                    gdriveBtn.classList.add('d-none');
                }
            } else {
                gdriveBtn.classList.add('d-none');
            }
        }

        eventFilter.addEventListener('change', filterPhotos);
        typeFilters.forEach(radio => radio.addEventListener('change', filterPhotos));
    </script>
</body>
</html>
