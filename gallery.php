<?php
session_start();
require_once 'includes/header.php';

function formatCategory($category) {
    $categories = [
        'renovations' => 'Renovations',
        'adaptations' => 'Adaptations',
        'tilework' => 'Tile Work',
        'insulation' => 'Insulation',
        'plumbing' => 'Plumbing Works',
        'roughConstruction' => 'Rough Construction',
        'flooring' => 'Flooring',
        'electrical' => 'Electrical Installation'
    ];
    return $categories[$category] ?? $category;
}

// ========== PRETRAGA I FILTRIRANJE ==========
$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');

// Osnovni upit
$sql = "SELECT * FROM projects WHERE active = 1";
$params = [];

// Filter po pretrazi
if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Filter po kategoriji
if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

$sql .= " ORDER BY created_at DESC";

try {
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $projects = $stmt->fetchAll();
} catch (PDOException $e) {
    $projects = [];
    $error = "Greška pri učitavanju projekata.";
}
?>

<section class="container-fluid py-5 gallery-section">
    <div class="container">
        <h2 class="text-center mb-5 crna-slova ">Our Projects</h2>

        <!-- FORMA ZA PRETRAGU -->
        <form method="GET" class="row g-3 mb-5">
            <div class="col-12 col-md-5">
                <input type="text" name="search" class="form-control" 
                    placeholder="Search projects..." 
                    value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-12 col-md-4">
                <select name="category" class="form-control">
                    <option value="">All Categories</option>
                    <option value="renovations" <?php echo $category === 'renovations' ? 'selected' : ''; ?>>Renovations</option>
                    <option value="adaptations" <?php echo $category === 'adaptations' ? 'selected' : ''; ?>>Adaptations</option>
                    <option value="tilework" <?php echo $category === 'tilework' ? 'selected' : ''; ?>>Tile Work</option>
                    <option value="insulation" <?php echo $category === 'insulation' ? 'selected' : ''; ?>>Insulation</option>
                    <option value="plumbing" <?php echo $category === 'plumbing' ? 'selected' : ''; ?>>Plumbing</option>
                    <option value="roughConstruction" <?php echo $category === 'roughConstruction' ? 'selected' : ''; ?>>Rough Construction</option>
                    <option value="flooring" <?php echo $category === 'flooring' ? 'selected' : ''; ?>>Flooring</option>
                    <option value="electrical" <?php echo $category === 'electrical' ? 'selected' : ''; ?>>Electrical</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <button type="submit" class="btn-custom btn-yellow w-100">Search</button>
            </div>
        </form>

        <!-- PRIKAZ PROJEKATA -->
        <?php if (isset($error)): ?>
            <p class="text-danger"><?php echo $error; ?></p>
        <?php elseif (empty($projects)): ?>
            <p class="text-center crna-slova">No projects found.</p>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($projects as $project): ?>
                    <div class="col-12 col-md-4">
                        <div class="project-card">
                            <div class="project-item project-tall">
                                <img src="/WEB_SAJTOVI_PHP/felix_php/uploads/<?php echo htmlspecialchars($project['image']); ?>" 
                                    alt="<?php echo htmlspecialchars($project['title']); ?>"
                                    class="project-image"
                                    onerror="this.src='/WEB_SAJTOVI_PHP/felix_php/images/indexslika1.jpg'">
                                <div class="project-overlay">
                                    <h3 class="project-name"><?php echo htmlspecialchars($project['title']); ?></h3>
                                </div>
                            </div>
                            <div class="p-3 bg-white">
                                <h5 class="crna-slova mb-1"><?php echo htmlspecialchars($project['title']); ?></h5>
                                <p class="crna-slova mb-1"><strong class="crna-slova"><?php echo formatCategory($project['category']); ?></strong></p>
                                <p class="crna-slova mb-1"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($project['location']); ?></p>
                                <p class="crna-slova"><?php echo htmlspecialchars($project['description']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>