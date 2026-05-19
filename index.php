<?php
require_once __DIR__ . '/includes/auth.php';
if (isLoggedIn()) {
    $r = $_SESSION['user']['role'];
    $map = ['admin'=>'/pages/admin/dashboard.php','enseignant'=>'/pages/enseignant/dashboard.php','etudiant'=>'/pages/etudiant/dashboard.php','responsable'=>'/pages/admin/dashboard.php'];
    header('Location: ' . baseUrl() . ($map[$r] ?? '/pages/login.php'));
} else {
    header('Location: ' . baseUrl() . '/pages/login.php');
}
exit;
