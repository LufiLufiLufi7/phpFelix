<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

// Statistika za dashboard
$totalProjects = $db->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$totalMessages = $db->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
$totalUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalPolls = $db->query("SELECT COUNT(*) FROM polls")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Felix Construction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/WEB_SAJTOVI_PHP/felix_php/css/style.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/WEB_SAJTOVI_PHP/felix_php/admin/index.php">
            <i class="fas fa-tools"></i> Felix Admin Panel
        </a>
        <div class="d-flex">
            <span class="navbar-text me-3 text-white">
                Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </span>
            <a href="/WEB_SAJTOVI_PHP/felix_php/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <nav class="col-md-2 bg-dark min-vh-100 p-3">
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a href="/WEB_SAJTOVI_PHP/felix_php/admin/index.php" class="nav-link text-white">
                        <i class="fas fa-dashboard"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="/WEB_SAJTOVI_PHP/felix_php/admin/projects.php" class="nav-link text-white">
                        <i class="fas fa-building"></i> Projects
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="/WEB_SAJTOVI_PHP/felix_php/admin/navigation.php" class="nav-link text-white">
                        <i class="fas fa-bars"></i> Navigation
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="/WEB_SAJTOVI_PHP/felix_php/admin/messages.php" class="nav-link text-white">
                        <i class="fas fa-envelope"></i> Messages
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="/WEB_SAJTOVI_PHP/felix_php/admin/polls.php" class="nav-link text-white">
                        <i class="fas fa-poll"></i> Polls
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="/WEB_SAJTOVI_PHP/felix_php/index.php" class="nav-link text-warning">
                        <i class="fas fa-home"></i> View Site
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 p-4">
            <h2 class="mb-4">Dashboard</h2>

            <div class="row g-4">
                <div class="col-12 col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-building"></i> Projects</h5>
                            <h2><?php echo $totalProjects; ?></h2>
                            <a href="/WEB_SAJTOVI_PHP/felix_php/admin/projects.php" class="text-white">Manage →</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="card text-white bg-dark">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-envelope"></i> Messages</h5>
                            <h2><?php echo $totalMessages; ?></h2>
                            <a href="/WEB_SAJTOVI_PHP/felix_php/admin/messages.php" class="text-white">View →</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="card text-white bg-secondary">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-users"></i> Users</h5>
                            <h2><?php echo $totalUsers; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-poll"></i> Polls</h5>
                            <h2><?php echo $totalPolls; ?></h2>
                            <a href="/WEB_SAJTOVI_PHP/felix_php/admin/polls.php" class="text-white">Manage →</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>