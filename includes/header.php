<?php
require_once __DIR__ . '/db.php';

$stmt = $db->prepare("SELECT * FROM navigation WHERE active = 1 ORDER BY position ASC");
$stmt->execute();
$navLinks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Felix Constructions | Professional Construction Services Since 1968</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <meta name="description" content="Felic Constructions offers top-quality construction services with over 50 years of experience.">
    <meta name="keywords" content="construction, building, renovation, Felic Constructions, construction company, professional builders, construction services">
    <meta name="author" content="Felic Constructions">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://www.felicconstructions.com/">
    <link rel="icon" href="/WEB_SAJTOVI_PHP/felix_php/images/image-removebg-preview.png" type="image/png">
    <link rel='stylesheet' type='text/css' href='/WEB_SAJTOVI_PHP/felix_php/css/style.css'>
    <link rel='stylesheet' type='text/css' href='/WEB_SAJTOVI_PHP/felix_php/css/responsive.css'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23/build/css/intlTelInput.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" />
</head>

<body>
    <section id="headerrr" class="video-bg">
        <video autoplay muted loop class="video-bg__content">
            <source src="/WEB_SAJTOVI_PHP/felix_php/video/12956438-uhd_2560_1440_30fps.mp4" type="video/mp4">
        </video>

        <header class="container-fluid navbar-light mutna-pozadina p-3">
            <nav class="navbar navbar-expand-lg">
                <div class="container">
                    <a href="/WEB_SAJTOVI_PHP/felix_php/index.php" class="navbar-brand text-white box-shadow">
                        <img src="/WEB_SAJTOVI_PHP/felix_php/images/image-removebg-preview.png" alt="Logo" width="130">
                    </a>
                    <button type="button" data-bs-toggle="collapse" data-bs-target="#nekiokvir" class="navbar-toggler">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="navbar-collapse collapse" id="nekiokvir">
                        <ul class="navbar-nav text-uppercase ms-auto text-white">
                            <?php foreach ($navLinks as $link): ?>
                                <li class="nav-item beliOkvir">
                                    <a href="<?php echo $link['url']; ?>" class="nav-link text-white">
                                        <?php echo $link['text']; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                    <li class="nav-item beliOkvir">
                                        <a href="/WEB_SAJTOVI_PHP/felix_php/admin/index.php" class="nav-link text-white">Admin</a>
                                    </li>
                                <?php endif; ?>
                                <li class="nav-item beliOkvir">
                                    <a href="/WEB_SAJTOVI_PHP/felix_php/logout.php" class="nav-link text-white">Logout</a>
                                </li>
                            <?php else: ?>
                                <li class="nav-item beliOkvir">
                                    <a href="/WEB_SAJTOVI_PHP/felix_php/login.php" class="nav-link text-white">Login</a>
                                </li>
                                <li class="nav-item beliOkvir">
                                    <a href="/WEB_SAJTOVI_PHP/felix_php/register.php" class="nav-link text-white">Register</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        <div class="container">
            <div class="row min-vh-80 d-flex align-items-center">
                <div class="col-12">
                    <div class="sadrzaj-text">
                        <h1 class="mb-3">Building a Concrete Future</h1>
                        <p class="mb-4">Honest, Reliable Builders since 1968</p>
                        <div class="d-flex flex-column flex-sm-row gap-3">
                            <a href="/WEB_SAJTOVI_PHP/felix_php/gallery.php" class="btn-custom btn-outline">View Projects</a>
                            <a href="/WEB_SAJTOVI_PHP/felix_php/contact.php" class="btn-custom btn-yellow">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>