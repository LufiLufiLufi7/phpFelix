<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$success = '';
$errors = [];

// ========== BRISANJE PROJEKTA ==========
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        // Obriši sliku ako postoji
        $stmt = $db->prepare("SELECT image FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $project = $stmt->fetch();
        if ($project && $project['image'] && file_exists(__DIR__ . '/../uploads/' . $project['image'])) {
            unlink(__DIR__ . '/../uploads/' . $project['image']);
        }
        $stmt = $db->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Project deleted successfully!';
    } catch (PDOException $e) {
        $errors[] = 'Error deleting project.';
    }
}

// ========== DODAVANJE/IZMENA PROJEKTA ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);
    $active = isset($_POST['active']) ? 1 : 0;

    if (empty($title)) $errors[] = 'Title is required';
    if (empty($category)) $errors[] = 'Category is required';
    if (empty($location)) $errors[] = 'Location is required';

    // Upload slike
    $imageName = $_POST['existing_image'] ?? '';
    if (!empty($_FILES['image']['name'])) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/avif'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            $errors[] = 'Only JPG, PNG, WEBP and AVIF images are allowed';
        } else {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../uploads/' . $imageName);
        }
    }

    if (empty($errors)) {
        try {
            if ($id > 0) {
                // Izmena
                $stmt = $db->prepare("UPDATE projects SET title=?, category=?, location=?, description=?, image=?, active=? WHERE id=?");
                $stmt->execute([$title, $category, $location, $description, $imageName, $active, $id]);
                $success = 'Project updated successfully!';
            } else {
                // Dodavanje
                $stmt = $db->prepare("INSERT INTO projects (title, category, location, description, image, active) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $category, $location, $description, $imageName, $active]);
                $success = 'Project added successfully!';
            }
        } catch (PDOException $e) {
            $errors[] = 'Error saving project.';
        }
    }
}

// Učitaj sve projekte
$projects = $db->query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll();

// Učitaj projekat za izmenu
$editProject = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editProject = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/WEB_SAJTOVI_PHP/felix_php/css/style.css">
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
                    <a href="/WEB_SAJTOVI_PHP/felix_php/admin/projects.php" class="nav-link text-warning">
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
            </ul>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 p-4">
            <h2 class="mb-4 text-dark">Manage Projects</h2>

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

            <!-- FORMA ZA DODAVANJE/IZMENU -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <strong><?php echo $editProject ? 'Edit Project' : 'Add New Project'; ?></strong>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <?php if ($editProject): ?>
                            <input type="hidden" name="id" value="<?php echo $editProject['id']; ?>">
                            <input type="hidden" name="existing_image" value="<?php echo $editProject['image']; ?>">
                        <?php endif; ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-dark">Title *</label>
                                <input type="text" name="title" class="form-control" required
                                    value="<?php echo $editProject ? htmlspecialchars($editProject['title']) : ''; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-dark">Category *</label>
                                <select name="category" class="form-control" required>
                                    <option value="">Select...</option>
                                    <?php
                                    $cats = ['renovations'=>'Renovations','adaptations'=>'Adaptations','tilework'=>'Tile Work','insulation'=>'Insulation','plumbing'=>'Plumbing Works','roughConstruction'=>'Rough Construction','flooring'=>'Flooring','electrical'=>'Electrical Installation'];
                                    foreach ($cats as $val => $label):
                                        $selected = ($editProject && $editProject['category'] === $val) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $val; ?>" <?php echo $selected; ?>><?php echo $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-dark">Location *</label>
                                <input type="text" name="location" class="form-control" required
                                    value="<?php echo $editProject ? htmlspecialchars($editProject['location']) : ''; ?>">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label text-dark">Description</label>
                                <textarea name="description" class="form-control" rows="3"><?php echo $editProject ? htmlspecialchars($editProject['description']) : ''; ?></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-dark">Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <?php if ($editProject && $editProject['image']): ?>
                                    <img src="/WEB_SAJTOVI_PHP/felix_php/uploads/<?php echo $editProject['image']; ?>"
                                        class="mt-2 img-thumbnail" style="max-height:80px">
                                <?php endif; ?>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-dark">Active</label>
                                <div class="form-check mt-2">
                                    <input type="checkbox" name="active" class="form-check-input" id="active"
                                        <?php echo (!$editProject || $editProject['active']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label text-dark" for="active">Active</label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning mt-3">
                            <?php echo $editProject ? 'Update Project' : 'Add Project'; ?>
                        </button>
                        <?php if ($editProject): ?>
                            <a href="/WEB_SAJTOVI_PHP/felix_php/admin/projects.php" class="btn btn-secondary mt-3">Cancel</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- LISTA PROJEKATA -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <strong>All Projects (<?php echo count($projects); ?>)</strong>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Location</th>
                                <th>Active</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project): ?>
                                <tr>
                                    <td>
                                        <img src="/WEB_SAJTOVI_PHP/felix_php/uploads/<?php echo $project['image']; ?>"
                                            class="img-thumbnail" style="max-height:50px"
                                            onerror="this.src='/WEB_SAJTOVI_PHP/felix_php/images/indexslika1.jpg'">
                                    </td>
                                    <td class="text-dark"><?php echo htmlspecialchars($project['title']); ?></td>
                                    <td class="text-dark"><?php echo htmlspecialchars($project['category']); ?></td>
                                    <td class="text-dark"><?php echo htmlspecialchars($project['location']); ?></td>
                                    <td>
                                        <?php if ($project['active']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="?edit=<?php echo $project['id']; ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?delete=<?php echo $project['id']; ?>"
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