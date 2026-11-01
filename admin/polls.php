<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$success = '';
$errors = [];

// ========== BRISANJE ANKETE ==========
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $db->prepare("DELETE FROM polls WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Poll deleted successfully!';
    } catch (PDOException $e) {
        $errors[] = 'Error deleting poll.';
    }
}

// ========== DODAVANJE ANKETE ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_poll'])) {
    $question = trim($_POST['question']);
    $active = isset($_POST['active']) ? 1 : 0;
    $options = array_filter(array_map('trim', $_POST['options']));

    if (empty($question)) $errors[] = 'Question is required';
    if (count($options) < 2) $errors[] = 'At least 2 options are required';

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO polls (question, active) VALUES (?, ?)");
            $stmt->execute([$question, $active]);
            $pollId = $db->lastInsertId();

            foreach ($options as $option) {
                $stmt = $db->prepare("INSERT INTO poll_options (poll_id, option_text) VALUES (?, ?)");
                $stmt->execute([$pollId, $option]);
            }
            $success = 'Poll added successfully!';
        } catch (PDOException $e) {
            $errors[] = 'Error saving poll.';
        }
    }
}

// ========== TOGGLE ACTIVE ==========
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $stmt = $db->prepare("UPDATE polls SET active = !active WHERE id = ?");
    $stmt->execute([$id]);
    $success = 'Poll status updated!';
}

// Učitaj sve ankete sa opcijama i glasovima
$polls = $db->query("SELECT * FROM polls ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Polls - Admin Panel</title>
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
                    <a href="/WEB_SAJTOVI_PHP/felix_php/admin/messages.php" class="nav-link text-white">
                        <i class="fas fa-envelope"></i> Messages
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="/WEB_SAJTOVI_PHP/felix_php/admin/polls.php" class="nav-link text-warning">
                        <i class="fas fa-poll"></i> Polls
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 p-4">
            <h2 class="mb-4 text-dark">Manage Polls</h2>

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

            <!-- FORMA ZA DODAVANJE -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <strong>Add New Poll</strong>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="add_poll" value="1">
                        <div class="mb-3">
                            <label class="form-label text-dark">Question *</label>
                            <input type="text" name="question" class="form-control" required
                                placeholder="Enter poll question...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark">Options * (minimum 2)</label>
                            <input type="text" name="options[]" class="form-control mb-2" placeholder="Option 1" required>
                            <input type="text" name="options[]" class="form-control mb-2" placeholder="Option 2" required>
                            <input type="text" name="options[]" class="form-control mb-2" placeholder="Option 3 (optional)">
                            <input type="text" name="options[]" class="form-control mb-2" placeholder="Option 4 (optional)">
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="active" class="form-check-input" id="active" checked>
                            <label class="form-check-label text-dark" for="active">Active</label>
                        </div>
                        <button type="submit" class="btn btn-warning">Add Poll</button>
                    </form>
                </div>
            </div>

            <!-- LISTA ANKETA -->
            <?php foreach ($polls as $poll): ?>
                <?php
                // Učitaj opcije i glasove za ovu anketu
                $stmt = $db->prepare("
                    SELECT po.*, COUNT(pv.id) as votes 
                    FROM poll_options po 
                    LEFT JOIN poll_votes pv ON po.id = pv.option_id 
                    WHERE po.poll_id = ? 
                    GROUP BY po.id
                ");
                $stmt->execute([$poll['id']]);
                $options = $stmt->fetchAll();
                $totalVotes = array_sum(array_column($options, 'votes'));
                ?>
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center
                        <?php echo $poll['active'] ? 'bg-success' : 'bg-secondary'; ?> text-white">
                        <strong><?php echo htmlspecialchars($poll['question']); ?></strong>
                        <div class="d-flex gap-2">
                            <a href="?toggle=<?php echo $poll['id']; ?>" class="btn btn-light btn-sm">
                                <?php echo $poll['active'] ? 'Deactivate' : 'Activate'; ?>
                            </a>
                            <a href="?delete=<?php echo $poll['id']; ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Delete this poll?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-dark mb-2">Total votes: <strong><?php echo $totalVotes; ?></strong></p>
                        <?php foreach ($options as $option): ?>
                            <?php $percent = $totalVotes > 0 ? round(($option['votes'] / $totalVotes) * 100) : 0; ?>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span class="text-dark"><?php echo htmlspecialchars($option['option_text']); ?></span>
                                    <span class="text-dark"><?php echo $option['votes']; ?> votes (<?php echo $percent; ?>%)</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-warning" style="width: <?php echo $percent; ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($polls)): ?>
                <div class="alert alert-info">No polls yet.</div>
            <?php endif; ?>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>