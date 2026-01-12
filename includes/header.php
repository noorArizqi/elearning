<?php
require_once __DIR__ . '/auth.php';
$user = get_user_info($pdo, $_SESSION['user_id']);

// Hitung notifikasi
$notif_count = 0;
if (is_logged_in()) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE id_user = ? AND is_read = 0");
    $stmt->execute([$_SESSION['user_id']]);
    $notif_count = $stmt->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eLearning - <?= ucfirst($_SESSION['role']) ?> Dashboard</title>
    <link rel="stylesheet" href="/elearning/assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Sistem eLearning</h1>
            <nav>
                <!-- Info user -->
                <span>Halo, <?= htmlspecialchars($user['nama_lengkap']) ?> (<?= ucfirst($user['role']) ?>)</span>

                <!-- Menu berdasarkan role -->
                <?php if ($_SESSION['role'] === 'mahasiswa'): ?>
                    <a href="/elearning/mahasiswa/dashboard.php">🏠 Dashboard</a>
                    <a href="/elearning/mahasiswa/view_attendance.php">📅 Absensi</a>
                    <a href="/elearning/mahasiswa/grades.php">📊 Nilai</a>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'dosen'): ?>
                    <a href="/elearning/dosen/dashboard.php">🏠 Dashboard</a>
                    <a href="/elearning/dosen/manage_class.php?id=1">📚 Kelas</a>
                    <a href="/elearning/dosen/forum/view_forum.php?id=1">💬 Forum</a>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="/elearning/admin/dashboard.php">🏠 Dashboard</a>
                    <a href="#">👥 Pengguna</a>
                <?php endif; ?>

                <!-- Notifikasi (opsional) -->
                <?php if ($notif_count > 0): ?>
                    <a href="/elearning/notifications.php" style="color:red;">🔔 (<?= $notif_count ?>)</a>
                <?php else: ?>
                    <a href="/elearning/notifications.php">🔔</a>
                <?php endif; ?>

                <!-- Logout -->
                <a href="/elearning/logout.php">🚪 Logout</a>
            </nav>
        </div>
    </header>

    <div class="container main-content">