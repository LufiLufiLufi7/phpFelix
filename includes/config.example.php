<?php
// ========================================
// CONFIG.EXAMPLE.PHP
// Kopiraj ovaj fajl u config.php i upiši svoje podatke.
// config.php se NE šalje na GitHub (nalazi se u .gitignore).
// ========================================

// Baza podataka
define('DB_HOST', 'localhost');
define('DB_NAME', 'felix-construction-php');
define('DB_USER', 'root');
define('DB_PASS', '');

// PHPMailer (Gmail SMTP + App Password)
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'your-email@gmail.com');
define('MAIL_PASSWORD', 'your-gmail-app-password');
define('MAIL_FROM_NAME', 'Felix Construction');

// Osnovni URL sajta (koristi se u aktivacionom linku)
define('APP_URL', 'http://localhost/WEB_SAJTOVI_PHP/felix_php');
