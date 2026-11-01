<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /WEB_SAJTOVI_PHP/felix_php/login.php");
    exit();
}
?>