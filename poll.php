<?php
require_once 'includes/header.php';

$errors = [];
$success = '';

// Učitaj aktivne ankete
$polls = $db->query("SELECT * FROM polls WHERE active = 1 ORDER BY created_at DESC")->fetchAll();

// ========== GLASANJE ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['poll_id'], $_POST['option_id'])) {
    $pollId = (int)$_POST['poll_id'];
    $optionId = (int)$_POST['option_id'];

    // Provjeri da li je korisnik ulogovan
    if (!isset($_SESSION['user_id'])) {
        $errors[] = 'You must be logged in to vote!';
    } else {
        // Provjeri da li je već glasao
        $stmt = $db->prepare("SELECT id FROM poll_votes WHERE poll_id = ? AND user_id = ?");
        $stmt->execute([$pollId, $_SESSION['user_id']]);

        if ($stmt->fetch()) {
            $errors[] = 'You have already voted on this poll!';
        } else {
            try {
                $stmt = $db->prepare("INSERT INTO poll_votes (poll_id, option_id, user_id) VALUES (?, ?, ?)");
                $stmt->execute([$pollId, $optionId, $_SESSION['user_id']]);
                $success = 'Your vote has been recorded!';
            } catch (PDOException $e) {
                $errors[] = 'Error recording vote.';
            }
        }
    }
}
?>

<section class="container-fluid py-5 bg-white">
    <div class="container">
        <h2 class="text-center mb-5 crna-slova">Polls</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p class="mb-0"><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (empty($polls)): ?>
            <p class="text-center crna-slova">No active polls at the moment.</p>
        <?php else: ?>
            <?php foreach ($polls as $poll): ?>
                <?php
                // Učitaj opcije sa brojem glasova
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

                // Provjeri da li je korisnik već glasao
                $hasVoted = false;
                if (isset($_SESSION['user_id'])) {
                    $stmt = $db->prepare("SELECT id FROM poll_votes WHERE poll_id = ? AND user_id = ?");
                    $stmt->execute([$poll['id'], $_SESSION['user_id']]);
                    $hasVoted = (bool)$stmt->fetch();
                }
                ?>

                <div class="card mb-4 mx-auto" style="max-width: 700px;">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><?php echo htmlspecialchars($poll['question']); ?></h5>
                    </div>
                    <div class="card-body">
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <div class="alert alert-warning">
                                Please <a href="/WEB_SAJTOVI_PHP/felix_php/login.php">login</a> to vote.
                            </div>
                        <?php endif; ?>

                        <?php if ($hasVoted || !isset($_SESSION['user_id'])): ?>
                            <!-- Prikaz rezultata -->
                            <p class="crna-slova mb-3">Total votes: <strong><?php echo $totalVotes; ?></strong></p>
                            <?php foreach ($options as $option): ?>
                                <?php $percent = $totalVotes > 0 ? round(($option['votes'] / $totalVotes) * 100) : 0; ?>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="crna-slova"><?php echo htmlspecialchars($option['option_text']); ?></span>
                                        <span class="crna-slova"><?php echo $option['votes']; ?> (<?php echo $percent; ?>%)</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" style="width: <?php echo $percent; ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if ($hasVoted): ?>
                                <p class="text-success mt-2"><i class="fas fa-check"></i> You have already voted.</p>
                            <?php endif; ?>

                        <?php else: ?>
                            <!-- Forma za glasanje -->
                            <form method="POST">
                                <input type="hidden" name="poll_id" value="<?php echo $poll['id']; ?>">
                                <?php foreach ($options as $option): ?>
                                    <div class="form-check mb-3">
                                        <input type="radio" name="option_id" 
                                            value="<?php echo $option['id']; ?>"
                                            class="form-check-input" 
                                            id="option_<?php echo $option['id']; ?>" required>
                                        <label class="form-check-label crna-slova" for="option_<?php echo $option['id']; ?>">
                                            <?php echo htmlspecialchars($option['option_text']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                                <button type="submit" class="btn-custom btn-yellow mt-2">Vote</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>