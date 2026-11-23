<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if ($isDemoAdmin) {
    $users = [
        ['id'=>1,'name'=>'Demo Administrator','email'=>'admin@example.com','role'=>'admin','active'=>1,'created_at'=>'2026-01-01'],
        ['id'=>2,'name'=>'Demo Customer','email'=>'customer@example.com','role'=>'user','active'=>1,'created_at'=>'2026-01-02']
    ];
} else {
    $stmt = $db->query("SELECT id, name, email, role, active, created_at FROM users ORDER BY id ASC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Users - Felix Construction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark px-4">
    <a class="navbar-brand" href="/admin/index.php">
        <i class="fas fa-tools"></i> Felix Admin
    </a>
    <div class="d-flex gap-3">
        <a href="/index.php" class="btn btn-outline-light btn-sm">
            <i class="fas fa-home"></i> View Site
        </a>
        <a href="/logout.php" class="btn btn-danger btn-sm">
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
                    <a href="/admin/index.php" class="nav-link text-white">
                        <i class="fas fa-dashboard"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="/admin/projects.php" class="nav-link text-white">
                        <i class="fas fa-building"></i> Projects
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="/admin/navigation.php" class="nav-link text-white">
                        <i class="fas fa-bars"></i> Navigation
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="/admin/messages.php" class="nav-link text-white">
                        <i class="fas fa-envelope"></i> Messages
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="/admin/polls.php" class="nav-link text-white">
                        <i class="fas fa-poll"></i> Polls
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="/admin/users.php" class="nav-link text-warning">
                        <i class="fas fa-users"></i> Users
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 p-4">
<?php if ($isDemoAdmin): ?><div class="alert alert-warning">Admin Assistant — read-only portfolio demo. User and message data are fictional.</div><?php endif; ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0"><i class="fas fa-users me-2"></i>Manage Users</h1>
        <span class="badge bg-secondary"><?php echo count($users); ?> users</span>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark"><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Registered</th></tr></thead>
                    <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo (int)$user['id']; ?></td>
                            <td><?php echo htmlspecialchars((string)$user['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars((string)$user['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span class="badge <?php echo $user['role'] === 'admin' ? 'bg-warning text-dark' : 'bg-secondary'; ?>"><?php echo htmlspecialchars((string)$user['role'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td><span class="badge <?php echo (int)$user['active'] === 1 ? 'bg-success' : 'bg-danger'; ?>"><?php echo (int)$user['active'] === 1 ? 'Active' : 'Inactive'; ?></span></td>
                            <td><?php echo htmlspecialchars((string)($user['created_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$users): ?><tr><td colspan="6" class="text-center py-4">No users found.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <p class="text-muted mt-3 small">User details are read-only. Passwords are never displayed.</p>
</main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>