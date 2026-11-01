<?php
// ========================================
// DB.PHP
// Konekcija na bazu podataka
// ========================================

// Podaci o bazi i mejlu se nalaze u config.php (vidi config.example.php)
require_once __DIR__ . '/config.php';

try {
    // new PDO() kreira konekciju na bazu
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS
    );

    // Postavi PDO da baca exceptions pri greškama
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vraća rezultate kao asocijativni niz
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Greška pri konekciji na bazu: " . $e->getMessage());
}
?>