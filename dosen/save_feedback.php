<?php
session_start();
require_once '../includes/config.php';

if ($_POST) {
    $submission_id = $_POST['submission_id'];
    $nilai = $_POST['nilai'] ?? null;
    $feedback = $_POST['feedback'] ?? '';

    // Validasi nilai
    if ($nilai !== null && !is_numeric($nilai)) {
        die("Nilai harus angka.");
    }

    $stmt = $pdo->prepare("UPDATE submissions SET nilai = ?, feedback_dosen = ? WHERE id = ?");
    $stmt->execute([$nilai, $feedback, $submission_id]);

    header("Location: view_submissions.php?assignment_id=" . $_POST['assignment_id']);
    exit;
} else {
    die("Data tidak valid.");
}
?>