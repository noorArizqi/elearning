<?php
session_start();
require_once '../includes/config.php';

if ($_GET['id']) {
    $course_id = $_GET['id'];

    // Cek apakah MK ada
    $stmt = $pdo->prepare("SELECT 1 FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);
    if (!$stmt->fetch()) {
        die("Mata kuliah tidak ditemukan.");
    }

    // Hapus dari tabel courses (cascade akan hapus kelas & materi & tugas)
    $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);

    header("Location: manage_courses.php");
    exit;
} else {
    die("ID tidak valid.");
}
?>