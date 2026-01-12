<?php
session_start();
require_once '../includes/config.php';

if ($_GET['id']) {
    $user_id = $_GET['id'];

    // Cek apakah user ada
    $stmt = $pdo->prepare("SELECT 1 FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    if (!$stmt->fetch()) {
        die("Pengguna tidak ditemukan.");
    }

    // Hapus dari tabel users (cascade akan hapus profile & enrollments)
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    header("Location: manage_users.php");
    exit;
} else {
    die("ID tidak valid.");
}
?>