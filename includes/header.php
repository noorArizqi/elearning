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
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Header Biru ITESA */
        .header {
            background: #0044ff; /* Warna biru solid ITESA */
            color: white;
            padding: 1rem 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-left: 20px;
        }

        .nav-menu {
            display: flex;
            gap: 15px;
            margin-right: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-menu span,
        .nav-menu a {
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            transition: background 0.2s;
        }

        .nav-menu a:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .notif-alert {
            color: #ffcc00 !important; /* Kuning terang untuk notifikasi */
            font-weight: bold;
        }

        .logout-link {
            color: #ff9f43 !important; /* Oranye lembut untuk logout */
        }

        /* Konten Utama */
        .main-content {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
            color: #333;
        }
        .main-content h1 {
            font-size: 36px;
            color: #f7f7f7ff;
        }
        .main-content p {
            color: #f7f7f7ff;
        }
        
        /* Responsif */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 10px;
                padding: 12px 0;
            }
            .nav-menu {
                justify-content: center;
                width: 100%;
                margin: 0;
            }
            .header h1 {
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Header Dinamis -->
    <div class="header">
        <h1>Sistem eLearning</h1>
        <div class="nav-menu">
            <!-- Info User -->
            <span>
                <i class="fas fa-user"></i>
                Halo, <?= htmlspecialchars($user['nama_lengkap']) ?> (<?= ucfirst($user['role']) ?>)
            </span>

            <!-- Menu Berdasarkan Role -->
            <?php if ($_SESSION['role'] === 'mahasiswa'): ?>
                <a href="/elearning/mahasiswa/dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="/elearning/mahasiswa/view_attendance.php"><i class="fas fa-calendar-check"></i> Absensi</a>
                <a href="/elearning/mahasiswa/grades.php"><i class="fas fa-chart-line"></i> Nilai</a>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'dosen'): ?>
                <a href="/elearning/dosen/dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="/elearning/dosen/manage_class.php?id=1"><i class="fas fa-book"></i> Kelas</a>
                <a href="/elearning/dosen/forum/view_forum.php?id=1"><i class="fas fa-comments"></i> Forum</a>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="/elearning/admin/dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="/elearning/admin/manage_users.php"><i class="fas fa-users"></i> Pengguna</a>
            <?php endif; ?>

            <!-- Notifikasi -->
            <?php if ($notif_count > 0): ?>
                <a href="/elearning/notifications.php" class="notif-alert">
                    <i class="fas fa-bell"></i> (<?= $notif_count ?>)
                </a>
            <?php else: ?>
                <a href="/elearning/notifications.php">
                    <i class="fas fa-bell"></i> Notifikasi
                </a>
            <?php endif; ?>

            <!-- Logout -->
            <a href="/elearning/logout.php" class="logout-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="main-content">
        <!-- Konten dashboard Anda di sini -->
        <h1>Selamat datang di ITESA , <?= htmlspecialchars($user['nama_lengkap']) ?>!</h1>
        <p>Anda login sebagai <strong><?= ucfirst($_SESSION['role']) ?></strong>.</p>
    </div>

</body>
</html>