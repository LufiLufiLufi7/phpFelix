<?php
require_once 'includes/header.php';

require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm_password']);

    // Validacija
    if (empty($name)) $errors[] = 'Ime je obavezno';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email nije validan';
    if (strlen($password) < 6) $errors[] = 'Lozinka mora imati najmanje 6 karaktera';
    if ($password !== $confirm) $errors[] = 'Lozinke se ne poklapaju';

    if (empty($errors)) {
        // Proveri da li email već postoji
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errors[] = 'Email već postoji';
        } else {
            // Hash lozinke
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(32));

            $stmt = $db->prepare("INSERT INTO users (name, email, password, activation_token) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashedPassword, $token]);

            // Pošalji aktivacioni email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = MAIL_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = MAIL_USERNAME;
                $mail->Password = MAIL_PASSWORD;
                $mail->SMTPSecure = 'tls';
                $mail->Port = MAIL_PORT;

                $mail->setFrom(MAIL_USERNAME, MAIL_FROM_NAME);
                $mail->addAddress($email, $name);
                $mail->Subject = 'Activate your Felix Construction account';
                $mail->Body = "Hello $name,\n\nClick the link to activate your account:\n\n" . APP_URL . "/activate.php?token=$token\n\nFelix Construction Team";

                $mail->send();
                $success = 'Registration successful! Please check your email to activate your account.';
            } catch (Exception $e) {
                $success = 'Registration successful! Activation email could not be sent.';
            }
        }
    }
}
?>

<section class="container-fluid pozslika3 py-5">
    <div class="container">
        <div class="col-12 col-md-6 mx-auto">
            <div class="kontakt-forma sadrzaj-text p-4">
                <h2 class="text-center mb-4">Register</h2>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-control forma-input" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control forma-input" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-control forma-input" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password *</label>
                        <input type="password" name="confirm_password" class="form-control forma-input" required>
                    </div>
                    <button type="submit" class="btn-custom btn-yellow w-100">Register</button>
                </form>
                <p class="text-center mt-3">Already have an account? <a href="/WEB_SAJTOVI_PHP/felix_php/login.php">Login</a></p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>