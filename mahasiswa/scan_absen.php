<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

$meeting_id = $_GET['meeting_id'] ?? 0;
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    die("<script>alert('Silakan login dulu'); window.location='/elearning/index.php';</script>");
}

// Cek apakah mahasiswa terdaftar di kelas ini
$stmt = $pdo->prepare("
    SELECT e.id_mahasiswa
    FROM enrollments e
    JOIN meetings m ON e.id_class = m.id_class
    WHERE m.id = ? AND e.id_mahasiswa = ?
");
$stmt->execute([$meeting_id, $user_id]);
if (!$stmt->fetch()) {
    die("<h2>❌ Anda tidak terdaftar di kelas ini!</h2>");
}

// Cek apakah sudah absen
$stmt = $pdo->prepare("SELECT 1 FROM attendance WHERE id_meeting = ? AND id_mahasiswa = ?");
$stmt->execute([$meeting_id, $user_id]);
if ($stmt->fetch()) {
    die("<h2>✅ Anda sudah absen hari ini!</h2>");
}

// Simpan absen
$stmt = $pdo->prepare("INSERT INTO attendance (id_meeting, id_mahasiswa, status) VALUES (?, ?, 'hadir')");
$stmt->execute([$meeting_id, $user_id]);

echo "<h2>✅ Absensi Berhasil!</h2>";
echo "<p>Terima kasih telah hadir.</p>";
echo '<meta http-equiv="refresh" content="3;url=/elearning/mahasiswa/dashboard.php">';
?>