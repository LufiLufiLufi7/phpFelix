<?php
session_start();
require_once 'includes/header.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $message = trim($_POST['message']);
    $service = trim($_POST['service']);
    $projectDate = trim($_POST['project_date']);
    $terms = isset($_POST['terms']) ? true : false;

    // Serverska validacija
    if (empty($name) || strlen($name) < 3) $errors[] = 'Name must be at least 3 characters';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
    if (!empty($phone) && !preg_match('/^[\+]?[(]?[0-9]{2,3}[)]?[-\s\.]?[0-9]{2,3}[-\s\.]?[0-9]{3,4}[-\s\.]?[0-9]{3,4}$/', $phone)) $errors[] = 'Invalid phone number format';
    if (empty($message) || strlen($message) < 10) $errors[] = 'Message must be at least 10 characters';
    if (empty($service)) $errors[] = 'Please select a service';
    if (empty($projectDate)) $errors[] = 'Project start date is required';
    if (!$terms) $errors[] = 'You must accept the terms of use';

    // Validacija datuma - ne sme biti u proslosti
    if (!empty($projectDate) && strtotime($projectDate) < strtotime('today')) {
        $errors[] = 'Project date cannot be in the past';
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO contact_messages (name, email, phone, message, service, project_date) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $message, $service, $projectDate]);
            $success = 'Your message has been sent successfully!';
        } catch (PDOException $e) {
            $errors[] = 'Error sending message. Please try again.';
        }
    }
}
?>

<section class="container-fluid pozslika3 py-5">
    <div class="container">
        <div class="col-12 col-md-8 mx-auto">
            <div class="kontakt-forma sadrzaj-text p-4">
                <h2 class="text-center mb-4">Contact Us</h2>
                <p class="text-center mb-4">Get in touch with us for your next project</p>

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

                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label">Full Name *</label>
                        <input type="text" id="name" name="name" class="form-control forma-input"
                            placeholder="Your Full Name" required
                            value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                        <div class="error-message"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" id="email" name="email" class="form-control forma-input"
                            placeholder="your@email.com" required
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        <div class="error-message"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control forma-input"
                            placeholder="+381 64 123 4567"
                            value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                        <div class="error-message"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message *</label>
                        <textarea id="message" name="message" class="form-control forma-input" rows="5"
                            placeholder="Describe your project..." required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        <div class="error-message"></div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="terms" class="form-check-input forma-input" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I accept the terms of use *
                            </label>
                        </div>
                        <div class="error-message"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Service of Interest *</label>
                        <select id="service" name="service" class="form-control forma-input" required>
                            <option value="" disabled selected>Select a service...</option>
                            <option value="adaptations" <?php echo (isset($_POST['service']) && $_POST['service'] === 'adaptations') ? 'selected' : ''; ?>>Adaptations</option>
                            <option value="renovations" <?php echo (isset($_POST['service']) && $_POST['service'] === 'renovations') ? 'selected' : ''; ?>>Renovations</option>
                            <option value="roughConstruction" <?php echo (isset($_POST['service']) && $_POST['service'] === 'roughConstruction') ? 'selected' : ''; ?>>Rough Construction</option>
                            <option value="doors" <?php echo (isset($_POST['service']) && $_POST['service'] === 'doors') ? 'selected' : ''; ?>>Doors</option>
                            <option value="windows" <?php echo (isset($_POST['service']) && $_POST['service'] === 'windows') ? 'selected' : ''; ?>>Windows</option>
                            <option value="flooring" <?php echo (isset($_POST['service']) && $_POST['service'] === 'flooring') ? 'selected' : ''; ?>>Flooring</option>
                            <option value="tilework" <?php echo (isset($_POST['service']) && $_POST['service'] === 'tilework') ? 'selected' : ''; ?>>Tile Work</option>
                            <option value="electrical" <?php echo (isset($_POST['service']) && $_POST['service'] === 'electrical') ? 'selected' : ''; ?>>Electrical Installation</option>
                            <option value="plumbing" <?php echo (isset($_POST['service']) && $_POST['service'] === 'plumbing') ? 'selected' : ''; ?>>Plumbing Works</option>
                            <option value="insulation" <?php echo (isset($_POST['service']) && $_POST['service'] === 'insulation') ? 'selected' : ''; ?>>Insulation</option>
                        </select>
                        <div class="error-message"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Project Start Date *</label>
                        <input type="date" id="project_date" name="project_date" class="form-control forma-input" required
                            value="<?php echo isset($_POST['project_date']) ? htmlspecialchars($_POST['project_date']) : ''; ?>">
                        <div class="error-message"></div>
                    </div>
                    <button type="submit" class="btn btn-custom btn-yellow w-100">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>