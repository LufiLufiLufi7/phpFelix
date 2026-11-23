<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$role = $_SESSION['user_role'] ?? '';
if (!isset($_SESSION['user_id']) || !in_array($role, ['admin', 'adminAsistent'], true)) {
    header('Location: /login.php');
    exit;
}
$isDemoAdmin = $role === 'adminAsistent';
if ($isDemoAdmin) {
    $allowed = ['index.php', 'projects.php', 'navigation.php', 'messages.php', 'polls.php', 'users.php'];
    if (!in_array(basename($_SERVER['SCRIPT_NAME']), $allowed, true) || $_SERVER['REQUEST_METHOD'] !== 'GET' || !empty($_GET)) {
        http_response_code(403);
        exit('Demo access is read-only.');
    }
}
