<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$stmt = $db->query("SELECT id, name, email, role, active, created_at FROM users ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a href="/admin/index.php" class="navbar-brand"><i class="fas fa-tools"></i> Felix Admin Panel</a>
        <a href="/admin/index.php" class="btn btn-outline-light btn-sm">Back to Dashboard</a>
    </div>
</nav>
<main class="container py-5">
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
</body>
</html>