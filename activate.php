<?php
require_once 'includes/db.php';

$message = '';
$success = false;

if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];

    // Pronađi korisnika sa tim tokenom
    $stmt = $db->prepare("SELECT id FROM users WHERE activation_token = ? AND active = 0");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        // Aktiviraj nalog
        $stmt = $db->prepare("UPDATE users SET active = 1, activation_token = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);
        $success = true;
        $message = 'Your account has been activated successfully! You can now login.';
    } else {
        $message = 'Invalid or already used activation link.';
    }
} else {
    $message = 'No activation token provided.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Activation - Felix Construction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel='stylesheet' type='text/css' href='/WEB_SAJTOVI_PHP/felix_php/css/style.css'>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="col-12 col-md-6 mx-auto text-center">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                    <h4><?php echo $message; ?></h4>
                    <a href="/WEB_SAJTOVI_PHP/felix_php/login.php" class="btn-custom btn-yellow mt-3">Login Now</a>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    <h4><?php echo $message; ?></h4>
                    <a href="/WEB_SAJTOVI_PHP/felix_php/index.php" class="btn-custom btn-yellow mt-3">Go Home</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>