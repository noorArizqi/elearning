<?php
require_once 'includes/auth.php';

// Redirect ke dashboard spesifik berdasarkan role
switch ($_SESSION['role']) {
    case 'admin':
        header('Location: admin/dashboard.php');
        break;
    case 'dosen':
        header('Location: dosen/dashboard.php');
        break;
    case 'mahasiswa':
        header('Location: mahasiswa/dashboard.php');
        break;
    default:
        session_destroy();
        header('Location: index.php');
}
exit;
?>