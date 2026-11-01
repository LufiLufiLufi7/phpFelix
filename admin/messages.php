<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$success = '';

// Brisanje poruke
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $db->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Message deleted successfully!';
    } catch (PDOException $e) {
        $success = 'Error deleting message.';
    }
}

// Učitaj sve poruke
$messages = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark px-4">
    <a class="navbar-brand" href="/WEB_SAJTOVI_PHP/felix_php/admin/index.php">
        <i class="fas fa-tools"></i> Felix Admin
    </a>
    <div class="d-flex gap-3">
        <a href="/WEB_SAJTOVI_PHP/felix_php/index.php" class="btn btn-outline-light btn-sm">
            <i class="fas fa-home"></i> View Site
        </a>
        <a href="/WEB_SAJTOVI_PHP/felix_php/logout.php" class="btn btn-danger btn-sm">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
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
                    <a href="/WEB_SAJTOVI_PHP/felix_php/admin/messages.php" class="nav-link text-warning">
                        <i class="fas fa-envelope"></i> Messages
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="/WEB_SAJTOVI_PHP/felix_php/admin/polls.php" class="nav-link text-white">
                        <i class="fas fa-poll"></i> Polls
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 p-4">
            <h2 class="mb-4 text-dark">Contact Messages</h2>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (empty($messages)): ?>
                <div class="alert alert-info">No messages yet.</div>
            <?php else: ?>
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <strong>All Messages (<?php echo count($messages); ?>)</strong>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Service</th>
                                    <th>Date</th>
                                    <th>Message</th>
                                    <th>Received</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($messages as $msg): ?>
                                    <tr>
                                        <td class="text-dark"><?php echo htmlspecialchars($msg['name']); ?></td>
                                        <td class="text-dark"><?php echo htmlspecialchars($msg['email']); ?></td>
                                        <td class="text-dark"><?php echo htmlspecialchars($msg['phone'] ?? '-'); ?></td>
                                        <td class="text-dark"><?php echo htmlspecialchars($msg['service'] ?? '-'); ?></td>
                                        <td class="text-dark"><?php echo htmlspecialchars($msg['project_date'] ?? '-'); ?></td>
                                        <td class="text-dark"><?php echo htmlspecialchars(substr($msg['message'], 0, 50)) . '...'; ?></td>
                                        <td class="text-dark"><?php echo date('d.m.Y H:i', strtotime($msg['created_at'])); ?></td>
                                        <td>
                                            <a href="?delete=<?php echo $msg['id']; ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Delete this message?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>