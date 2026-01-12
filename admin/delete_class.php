<?php
session_start();
require_once '../includes/config.php';

if ($_GET['id']) {
    $class_id = $_GET['id'];

    // Cek apakah kelas ada
    $stmt = $pdo->prepare("SELECT 1 FROM classes WHERE id = ?");
    $stmt->execute([$class_id]);
    if (!$stmt->fetch()) {
        die("Kelas tidak ditemukan.");
    }

    // Hapus dari tabel classes (cascade akan hapus kelas & semua data terkait)
    $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
    $stmt->execute([$class_id]);

    header("Location: manage_classes.php?success=1");
    exit;
} else {
    die("ID tidak valid.");
}
?>