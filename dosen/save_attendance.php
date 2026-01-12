<?php
session_start();
require_once '../includes/config.php';

if ($_POST) {
    $student_id = $_POST['student_id'];
    $meeting_id = $_POST['meeting_id'];
    $status = $_POST['status'];

    // Update atau insert
    $stmt = $pdo->prepare("
        INSERT INTO attendance (id_meeting, id_mahasiswa, status) 
        VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE status = VALUES(status), waktu_presensi = NOW()
    ");
    $stmt->execute([$meeting_id, $student_id, $status]);

    echo "Status absensi berhasil diperbarui.";
} else {
    echo "Gagal menyimpan.";
}
?>