<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$success = '';
$errors = [];

// ========== BRISANJE ==========
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $db->prepare("DELETE FROM navigation WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Link deleted successfully!';
    } catch (PDOException $e) {
        $errors[] = 'Error deleting link.';
    }
}

// ========== DODAVANJE/IZMENA ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $text = trim($_POST['text']);
    $url = trim($_POST['url']);
    $position = (int)$_POST['position'];
    $active = isset($_POST['active']) ? 1 : 0;

    if (empty($text)) $errors[] = 'Text is required';
    if (empty($url)) $errors[] = 'URL is required';

    if (empty($errors)) {
        try {
            if ($id > 0) {
                $stmt = $db->prepare("UPDATE navigation SET text=?, url=?, position=?, active=? WHERE id=?");
                $stmt->execute([$text, $url, $position, $active, $id]);
                $success = 'Link updated successfully!';
            } else {
                $stmt = $db->prepare("INSERT INTO navigation (text, url, position, active) VALUES (?, ?, ?, ?)");
                $stmt->execute([$text, $url, $position, $active]);
                $success = 'Link added successfully!';
            }
        } catch (PDOException $e) {
            $errors[] = 'Error saving link.';
        }
    }
}

// Učitaj sve linkove
$navLinks = $db->query("SELECT * FROM navigation ORDER BY position ASC")->fetchAll();

// Učitaj za izmenu
$editLink = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM navigation WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editLink = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation - Admin Panel</title>
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
                    <a href="/WEB_SAJTOVI_PHP/felix_php/admin/navigation.php" class="nav-link text-warning">
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
            </ul>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 p-4">
            <h2 class="mb-4 text-dark">Manage Navigation</h2>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $e): ?>
                        <p class="mb-0"><?php echo $e; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- FORMA -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <strong><?php echo $editLink ? 'Edit Link' : 'Add New Link'; ?></strong>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?php if ($editLink): ?>
                            <input type="hidden" name="id" value="<?php echo $editLink['id']; ?>">
                        <?php endif; ?>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label text-dark">Text *</label>
                                <input type="text" name="text" class="form-control" required
                                    value="<?php echo $editLink ? htmlspecialchars($editLink['text']) : ''; ?>">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label text-dark">URL *</label>
                                <input type="text" name="url" class="form-control" required
                                    value="<?php echo $editLink ? htmlspecialchars($editLink['url']) : ''; ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-dark">Position</label>
                                <input type="number" name="position" class="form-control"
                                    value="<?php echo $editLink ? $editLink['position'] : '0'; ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-dark">Active</label>
                                <div class="form-check mt-2">
                                    <input type="checkbox" name="active" class="form-check-input"
                                        <?php echo (!$editLink || $editLink['active']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label text-dark">Active</label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning mt-3">
                            <?php echo $editLink ? 'Update Link' : 'Add Link'; ?>
                        </button>
                        <?php if ($editLink): ?>
                            <a href="/WEB_SAJTOVI_PHP/felix_php/admin/navigation.php" class="btn btn-secondary mt-3">Cancel</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- LISTA -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <strong>All Navigation Links (<?php echo count($navLinks); ?>)</strong>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Text</th>
                                <th>URL</th>
                                <th>Position</th>
                                <th>Active</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($navLinks as $link): ?>
                                <tr>
                                    <td class="text-dark"><?php echo htmlspecialchars($link['text']); ?></td>
                                    <td class="text-dark"><?php echo htmlspecialchars($link['url']); ?></td>
                                    <td class="text-dark"><?php echo $link['position']; ?></td>
                                    <td>
                                        <?php if ($link['active']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="?edit=<?php echo $link['id']; ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?delete=<?php echo $link['id']; ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>