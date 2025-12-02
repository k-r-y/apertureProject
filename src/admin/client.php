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


global $conn;

$search_term = $_GET['search'] ?? '';

$sql = "SELECT userID, Email, FirstName, LastName, contactNo, `Role`, Status, ProfileCompleted, isVerified FROM users";
$params = [];
$types = '';

if (!empty($search_term)) {
    $sql .= " WHERE CONCAT(FirstName, ' ', LastName, ' ', userID, ' ', Email, ' ', contactNo, ' ', `Role`, ' ', Status, ' ', ProfileCompleted, ' ', isVerified) LIKE ? OR Email LIKE ?";
    $like_term = "%" . $search_term . "%";
    $params = [$like_term, $like_term];
    $types = 'ss';
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= generateCSRFToken() ?>">
    <title>Client Directory - Aperture Admin</title>
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
    <style>
        /* Fix for modal width issue */
        .modal-dialog {
            max-width: 500px;
            margin: 1.75rem auto;
        }
        @media (min-width: 576px) {
            .modal-dialog {
                max-width: 500px;
            }
        }
        @media (min-width: 992px) {
            .modal-lg {
                max-width: 800px;
            }
        }
    </style>

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
                    <h1 class="header-title m-0">Client Directory</h1>
                    <a href="#" class="btn btn-gold">+ Add New Client</a>
                </div>

                <!-- Filters -->
                <div class="neo-card mb-4">
                    <form action="client.php" method="GET">
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                            <div class="flex-grow-1">
                                <input type="text" name="search" class="neo-input" placeholder="Search by client name or email..." value="<?= htmlspecialchars($search_term) ?>">
                            </div>
                            <button type="submit" class="btn btn-ghost">Search</button>
                        </div>
                    </form>
                </div>

                <!-- Client Cards -->
                <div class="glass-panel p-4">
                    <div class="table-responsive">
                        <table class="table table-luxury align-middle">

                            <thead>
                                <tr>
                                    <th>UserID</th>
                                    <th>Email</th>
                                    <th>First_Name</th>
                                    <th>Last_Name</th>
                                    <th>Contact_No.</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Profile</th>
                                    <th>Verified</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($users as $user): ?>

                                    <tr data-user-id="<?= $user['userID'] ?>">
                                        <td><span class="text-muted">#<?= htmlspecialchars($user['userID']); ?></span></td>
                                        <td><?= htmlspecialchars($user['Email']); ?></td>
                                        <td class=" text-gold" style="min-width: 200px; max-width: 200px; "><?= isset($user['FirstName']) ? htmlspecialchars($user['FirstName']) : '-'; ?></td>
                                        <td class="text-gold"><?= isset($user['LastName']) ? htmlspecialchars($user['LastName']) : '-'; ?></td>
                                        <td><?= isset($user['contactNo']) ? htmlspecialchars($user['contactNo']) : '-'; ?></td>
                                        <td class="user-role">
                                            <?php 
                                                $roleClass = $user['Role'] === 'Admin' ? 'bg-danger' : 'bg-soft-gold text-gold';
                                            ?>
                                            <span class="badge <?= $roleClass ?>"><?= htmlspecialchars($user['Role']); ?></span>
                                        </td>
                                        <td class="user-status">
                                            <?php 
                                                $statusClass = 'status-pending';
                                                if ($user['Status'] === 'Active') $statusClass = 'status-confirmed';
                                                if ($user['Status'] === 'Inactive') $statusClass = 'status-overdue';
                                            ?>
                                            <span class="status-badge <?= $statusClass ?>" 
                                                  style="cursor: pointer;" 
                                                  onclick="toggleUserStatus(<?= $user['userID'] ?>, '<?= $user['Status'] ?>')" 
                                                  title="Click to toggle status">
                                                <?= htmlspecialchars($user['Status']); ?>
                                            </span>
                                        </td>
                                        <td class="user-profile">
                                            <?php if($user['ProfileCompleted']): ?>
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                            <?php else: ?>
                                                <i class="bi bi-x-circle-fill text-danger"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td class="user-verified">
                                            <?php if($user['isVerified']): ?>
                                                <i class="bi bi-patch-check-fill text-info"></i>
                                            <?php else: ?>
                                                <i class="bi bi-exclamation-circle text-warning"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-ghost p-2" 
                                                        onclick="viewUserDetails(<?= $user['userID'] ?>)" 
                                                        title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-ghost p-2" 
                                                        onclick="editUser(<?= $user['userID'] ?>)" 
                                                        title="Edit User">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- View User Modal -->
    <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-gold" id="viewUserModalLabel">
                        <i class="bi bi-person-circle me-2"></i>User Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="viewUserContent">
                    <!-- Content loaded via JavaScript -->
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-gold" id="editUserModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Edit User
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        <!-- Form loaded via JavaScript -->
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../libs/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="admin.js"></script>
    <script src="js/client-management.js"></script>
</body>

</html>