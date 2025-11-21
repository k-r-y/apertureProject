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
    <title>Client Directory - Aperture Admin</title>
   <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">

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
                <div class="card-solid mb-4">
                    <form action="client.php" method="GET">
                        <div class="card-body d-flex flex-wrap gap-3 align-items-center">
                            <div class="flex-grow-1">
                                <input type="text" name="search" class="form-control bg-dark border-secondary text-light" placeholder="Search by client name or email..." value="<?= htmlspecialchars($search_term) ?>">
                            </div>
                            <button type="submit" class="btn btn-outline-secondary">Search</button>
                        </div>
                    </form>
                </div>

                <!-- Client Cards -->
                <div class="card-solid">
                    <div class="table-responsive">
                        <table class="table  table-hover table  ">

                            <thead>
                                <tr>
                                    <th>UserID</th>
                                    <th>Email</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Contact No.</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>isProfileCompleted</th>
                                    <th>isVerified</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($users as $user): ?>

                                    <tr>
                                        <td><?= htmlspecialchars($user['userID']); ?></td>
                                        <td><?= htmlspecialchars($user['Email']); ?></td>
                                        <td><?= htmlspecialchars($user['FirstName']); ?></td>
                                        <td><?= htmlspecialchars($user['LastName']); ?></td>
                                        <td><?= htmlspecialchars($user['contactNo']); ?></td>
                                        <td><?= htmlspecialchars($user['Role']); ?></td>
                                        <td><?= htmlspecialchars($user['Status']); ?></td>
                                        <td><?= htmlspecialchars($user['ProfileCompleted'] ? 'Yes' : 'No'); ?></td>
                                        <td><?= htmlspecialchars($user['isVerified'] ? 'Yes' : 'No'); ?></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-secondary">View</a>
                                            <a href="#" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil-fill"></i></a>
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

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="admin.js"></script>
</body>

</html>