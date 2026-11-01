<?php
require_once 'includes/db.php'; // samo db, bez headera

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email)) $errors[] = 'Email je obavezan';
    if (empty($password)) $errors[] = 'Lozinka je obavezna';

    if (empty($errors)) {
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['active'] == 0) {
                $errors[] = 'Nalog nije aktiviran. Proverite email.';
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                header("Location: /WEB_SAJTOVI_PHP/felix_php/index.php");
                exit();
            }
        } else {
            $errors[] = 'Pogrešan email ili lozinka';
        }
    }
}

// Tek OVDJE, nakon sve logike, uključujemo header koji šalje HTML
require_once 'includes/header.php';
?>

<section class="container-fluid pozslika3 py-5">
    <div class="container">
        <div class="col-12 col-md-6 mx-auto">
            <div class="kontakt-forma sadrzaj-text p-4">
                <h2 class="text-center mb-4">Login</h2>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control forma-input" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-control forma-input" required>
                    </div>
                    <button type="submit" class="btn-custom btn-yellow w-100">Login</button>
                </form>
                <p class="text-center mt-3">Don't have an account? <a href="/WEB_SAJTOVI_PHP/felix_php/register.php">Register</a></p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>