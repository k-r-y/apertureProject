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

// Fetch photos with booking and package info
$sql = "
    SELECT 
        p.*, 
        b.event_type, 
        b.event_date, 
        b.gdrive_link,
        pkg.access_duration_months,
        (SELECT COUNT(*) FROM booking_addons ba JOIN addons a ON ba.addonID = a.addID WHERE ba.bookingID = b.bookingID AND a.Description LIKE '%Extended Access%') as hasExtendedAccess
    FROM user_photos p
    LEFT JOIN bookings b ON p.bookingID = b.bookingID
    LEFT JOIN packages pkg ON b.packageID = pkg.packageID
    WHERE p.userID = ?
    GROUP BY p.photoID
    ORDER BY p.uploadDate DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$photos = [];
while ($row = $result->fetch_assoc()) {
    // Calculate expiration
    if ($row['hasExtendedAccess'] > 0) {
        $row['expiration_status'] = 'lifetime';
        $row['days_left'] = PHP_INT_MAX;
    } else {
        $uploadDate = new DateTime($row['uploadDate']);
        $duration = (int)$row['access_duration_months'];
        $expirationDate = clone $uploadDate;
        $expirationDate->modify("+$duration months");
        
        $now = new DateTime();
        $interval = $now->diff($expirationDate);
        $daysLeft = (int)$interval->format('%r%a');
        
        $row['expiration_date'] = $expirationDate->format('M d, Y');
        $row['days_left'] = $daysLeft;
        
        if ($daysLeft < 0) {
            $row['expiration_status'] = 'expired';
        } elseif ($daysLeft <= 7) {
            $row['expiration_status'] = 'warning';
        } else {
            $row['expiration_status'] = 'normal';
        }
    }
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/photoswipe@5.3.8/dist/photoswipe.css">
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Old+Standard+TT:wght@400;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        .expiration-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            z-index: 2;
            backdrop-filter: blur(4px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            background-color: rgba(0, 0, 0, 0.73);
        }
        .badge-lifetime { background-color: rgba(25, 135, 84, 0.9); color: white; }
        .badge-warning { background-color: rgba(255, 193, 7, 0.9); color: #000; }
        .badge-expired { background-color: rgba(220, 53, 69, 0.9); color: white; }
        
        .gallery-item {
            position: relative;
            margin-bottom: 20px;
        }
        
        .gallery-item .gallery-link {
            display: block;
            width: 100% !important;
            height: 100% !important;
            overflow: hidden;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-radius: 8px;
            transition: none;
        }

        .gallery-link:hover{
            height: 100% !important;
            width: 100% !important;
            object-fit: cover !important;
        }

        .gallery-item .gallery-link:hover {
            opacity: 1 !important;
            transform: none !important;
            box-shadow: none !important;
        }

        /* Active State for Filter Buttons */
        .btn-check:checked + .btn-outline-gold {
            background-color: #d4af37;
            color: #000;
            border-color: #d4af37;
        }
        
        .btn-outline-gold {
            color: #d4af37;
            border-color: #d4af37;
        }
        
        .btn-outline-gold:hover {
            background-color: rgba(212, 175, 55, 0.1);
            color: #d4af37;
        }
    </style>
</head>
<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>

    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <div class="content">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-light serif mb-0">My Photos</h2>
                </div>

                <!-- Filter Controls -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="text-muted small mb-2 text-uppercase">Event</label>
                        <select class="form-select bg-dark text-light border-secondary" id="eventFilter">
                            <option value="all">All Events</option>
                            <?php foreach ($bookings as $booking): ?>
                                <option value="<?= $booking['bookingID'] ?>" data-link="<?= htmlspecialchars($booking['gdrive_link'] ?? '') ?>">
                                    <?= htmlspecialchars($booking['event_type']) ?> (<?= date('M d, Y', strtotime($booking['event_date'])) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-2 text-uppercase">Photo Type</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="typeFilter" id="typeAll" value="all" checked>
                            <label class="btn btn-outline-gold" for="typeAll">All</label>

                            <input type="radio" class="btn-check" name="typeFilter" id="typeEdited" value="edited">
                            <label class="btn btn-outline-gold" for="typeEdited">Edited</label>

                            <input type="radio" class="btn-check" name="typeFilter" id="typeRaw" value="raw">
                            <label class="btn btn-outline-gold" for="typeRaw">Raw</label>
                        </div>
                    </div>
                    <div class="col-md-2 text-end">
                        <a href="#" id="gdriveBtn" class="btn btn-outline-gold d-none" target="_blank">
                            <i class="bi bi-google me-2"></i>Drive
                        </a>
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
                                <?php
                                    $imagePath = '../../uploads/user_photos/' . $userID . '/' . $photo['fileName'];
                                    $serverPath = __DIR__ . '/../../uploads/user_photos/' . $userID . '/' . $photo['fileName'];
                                    $width = 100; // Default fallback
                                    $height = 100; // Default fallback
                                    
                                    if (file_exists($serverPath)) {
                                        list($w, $h) = getimagesize($serverPath);
                                        if ($w && $h) {
                                            $width = $w;
                                            $height = $h;
                                        }
                                    }
                                ?>
                                <a href="<?= $imagePath ?>" 
                                style="width: 100px; height: 100px; object-fit: cover;"
                                   class="gallery-link"
                                   data-pswp-width="<?= $width ?>" 
                                   data-pswp-height="<?= $height ?>" 
                                   target="_blank">
                                    <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($photo['caption']) ?>" loading="lazy">
                                </a>
                                
                                <!-- Photo Type Badge -->
                                <div class="photo-badge">
                                    <?= ucfirst($photo['photo_type'] ?? 'edited') ?>
                                </div>

                                <!-- Expiration Badge -->
                                <?php if ($photo['expiration_status'] === 'lifetime'): ?>
                                    <div class="expiration-badge badge-lifetime" title="Extended Access Add-on Active">
                                        <i class="bi bi-infinity"></i> Lifetime
                                    </div>
                                <?php elseif ($photo['expiration_status'] === 'expired'): ?>
                                    <div class="expiration-badge badge-expired">
                                        Expired
                                    </div>
                                <?php else: ?>
                                    <div class="expiration-badge <?= $photo['expiration_status'] === 'warning' ? 'badge-warning' : 'badge-normal' ?>" 
                                         title="Expires on <?= $photo['expiration_date'] ?>">
                                        <i class="bi bi-clock-history"></i> <?= $photo['days_left'] ?> days left
                                    </div>
                                <?php endif; ?>

                                <div class="gallery-overlay" style="display: none;">
                                    <div class="text-white small">
                                        <?= htmlspecialchars($photo['caption']) ?>
                                    </div>
                                    <a href="../../uploads/user_photos/<?= $userID ?>/<?= $photo['fileName'] ?>" download class="btn btn-sm btn-light rounded-circle">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                                <a href="../../uploads/user_photos/<?= $userID ?>/<?= $photo['fileName'] ?>" download class="btn btn-sm btn-light rounded-circle position-absolute bottom-0 end-0 m-2" style="z-index: 10;">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="user.js"></script>
    <script src="js/notifications.js"></script>
    <script src="js/user_notifications.js"></script>
    
    <script type="module">
        import PhotoSwipeLightbox from 'https://cdn.jsdelivr.net/npm/photoswipe@5.3.8/dist/photoswipe-lightbox.esm.js';
        import PhotoSwipe from 'https://cdn.jsdelivr.net/npm/photoswipe@5.3.8/dist/photoswipe.esm.js';

        const lightbox = new PhotoSwipeLightbox({
            gallery: '#galleryGrid',
            children: '.gallery-item .gallery-link', // Updated selector
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

        if (eventFilter) {
            eventFilter.addEventListener('change', filterPhotos);
        }
        typeFilters.forEach(radio => radio.addEventListener('change', filterPhotos));
    </script>
</body>
</html>
