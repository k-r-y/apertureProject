<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/function.php';
require_once '../includes/functions/csrf.php';
require_once '../includes/functions/session.php';

if (isset($_SESSION["userId"]) and isset($_SESSION["role"]) and $_SESSION["role"] === "User" and isset($_SESSION["isVerified"]) and  $_SESSION["isVerified"]) {
    header("Location: ../booking.php");
    exit;
}

if (!isset($_SESSION['userId']) or !isset($_SESSION['isVerified']) or $_SESSION['isVerified'] === 0) {
    header("Location: ../logIn.php");
    exit;
} else {
    $isProfileCompleted = isProfileCompleted($_SESSION['userId']);
    if (!$isProfileCompleted) {
        header("Location: ../completeProfile.php");
        exit;
    }
}

if (isset($_GET['action']) and $_GET['action'] === 'logout') {
    require_once '../includes/functions/auth.php';
    logout();
}

// Handle Package Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_package'])) {
    $pkgId = $_POST['package_id'];
    $name = sanitizeInput($_POST['name']);
    $price = sanitizeInput($_POST['price']);
    $desc = sanitizeInput($_POST['description']);

    $stmt = $conn->prepare("UPDATE packages SET PackageName = ?, Price = ?, Description = ? WHERE packageID = ?");
    $stmt->bind_param("sdss", $name, $price, $desc, $pkgId);
    
    if ($stmt->execute()) {
        $successMsg = "Package updated successfully.";
    } else {
        $errorMsg = "Failed to update package.";
    }
    $stmt->close();
}

// Fetch Packages
$packages = [];
$query = "SELECT * FROM packages";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Fetch Inclusions for this package
        $incQuery = "SELECT Description FROM inclusion WHERE packageID = ?";
        $incStmt = $conn->prepare($incQuery);
        $incStmt->bind_param("s", $row['packageID']);
        $incStmt->execute();
        $incResult = $incStmt->get_result();
        $inclusions = [];
        while ($inc = $incResult->fetch_assoc()) {
            $inclusions[] = $inc['Description'];
        }
        $row['inclusions'] = $inclusions;
        $packages[] = $row;
        $incStmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services & Packages - Aperture Admin</title>

    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../luxuryDesignSystem.css">
    <link rel="stylesheet" href="../css/modal.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>

    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content">
            <div class="container-fluid p-0">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="header-title m-0">Services & Packages</h1>
                    <!-- Add Button Removed as requested -->
                </div>

                <?php if (isset($successMsg)): ?>
                    <div class="alert alert-gold mb-4"><?= $successMsg ?></div>
                <?php endif; ?>

                <div class="row g-4">
                    <?php foreach ($packages as $pkg) : ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="neo-card h-100 d-flex flex-column">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h4 class="text-gold font-serif mb-0"><?= htmlspecialchars($pkg['packageName']) ?></h4>
                                        <span class="text-light fs-5 fw-bold">₱<?= number_format($pkg['Price']) ?></span>
                                    </div>
                                    <p class="text-muted small mb-4"><?= htmlspecialchars($pkg['description']) ?></p>
                                    
                                    <div class="mb-3">
                                        <h6 class="text-gold small text-uppercase letter-spacing-1 mb-2">Inclusions</h6>
                                        <ul class="text-muted small p-0" style="list-style-type: none;">
                                            <?php foreach ($pkg['inclusions'] as $inc) : ?>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-gold me-2"></i><?= htmlspecialchars($inc) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                                <div class="mt-3 pt-3 border-top border-secondary">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-gold w-100 gold-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editPackageModal"
                                            data-id="<?= $pkg['packageID'] ?>"
                                            data-name="<?= htmlspecialchars($pkg['packageName']) ?>"
                                            data-price="<?= $pkg['Price'] ?>"
                                            data-desc="<?= htmlspecialchars($pkg['description']) ?>">
                                        Edit Package
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </main>
    </div>

    <!-- Edit Package Modal -->
    <div class="modal fade" id="editPackageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-panel border-0">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title text-gold font-serif">Edit Package</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="package_id" id="edit_package_id">
                        <input type="hidden" name="update_package" value="1">
                        
                        <div class="mb-3">
                            <label class="text-muted small mb-2">Package Name</label>
                            <input type="text" name="name" id="edit_name" class="neo-input" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-muted small mb-2">Price (₱)</label>
                            <input type="number" name="price" id="edit_price" class="neo-input" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-muted small mb-2">Description</label>
                            <textarea name="description" id="edit_desc" class="neo-input" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-secondary">
                        <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gold">Save Changes</button>
                    </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/notifications.js"></script>
    <script>
        var editModal = document.getElementById('editPackageModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const price = button.getAttribute('data-price');
            const desc = button.getAttribute('data-desc');
            
            editModal.querySelector('#edit_package_id').value = id;
            editModal.querySelector('#edit_name').value = name;
            editModal.querySelector('#edit_price').value = price;
            editModal.querySelector('#edit_desc').value = desc;
        });
    </script>
</body>

</html>