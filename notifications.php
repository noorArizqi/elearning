<?php
session_start();
require_once 'includes/header.php';
require_login();

// Ambil notifikasi
$stmt = $pdo->prepare("
    SELECT n.*, u.nama_lengkap AS pengirim
    FROM notifications n
    JOIN users u ON n.id_from = u.id
    WHERE n.id_user = ?
    ORDER BY n.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll();
?>

<style>
    .page-title {
        font-size: 24px;
        color: #2c3e50;
        margin: 20px 0 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .notification-list {
        max-width: 800px;
        margin: 0 auto;
    }

    .notification-item {
        background: white;
        border-radius: 10px;
        padding: 18px;
        margin-bottom: 16px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.06);
        border-left: 4px solid #ccc;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .notification-item.unread {
        border-left-color: #0044ff;
        background: #f0f7ff;
    }

    .notification-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    .notification-sender {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 6px;
        font-size: 15px;
    }

    .notification-message {
        color: #444;
        margin: 8px 0;
        line-height: 1.5;
    }

    .notification-time {
        color: #7f8c8d;
        font-size: 13px;
        margin-top: 8px;
    }

    .notification-link,
    .mark-read-btn {
        display: inline-block;
        margin-top: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
    }

    .notification-link {
        color: #0044ff;
    }

    .notification-link:hover {
        text-decoration: underline;
    }

    .mark-read-btn {
        background: none;
        border: none;
        color: #0044ff;
        cursor: pointer;
        padding: 0;
        font-weight: 600;
    }

    .mark-read-btn:hover {
        text-decoration: underline;
    }

    .no-notif {
        text-align: center;
        color: #6c757d;
        font-style: italic;
        padding: 40px 20px;
        max-width: 600px;
        margin: 30px auto;
    }

    .back-link {
        display: inline-block;
        margin-top: 20px;
        color: #0044ff;
        text-decoration: none;
        font-weight: 600;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .notification-item {
            padding: 16px;
        }
    }
</style>

<div class="container">
    <h2 class="page-title">🔔 Notifikasi Anda</h2>

    <?php if ($notifications): ?>
        <div class="notification-list">
            <?php foreach ($notifications as $n): ?>
                <div class="notification-item <?= !$n['is_read'] ? 'unread' : '' ?>">
                    <div class="notification-sender">
                        <?= htmlspecialchars($n['pengirim']) ?>
                    </div>
                    <div class="notification-message">
                        <?= htmlspecialchars($n['message']) ?>
                    </div>
                    <div class="notification-time">
                        <?= date('d M Y H:i', strtotime($n['created_at'])) ?>
                    </div>
                    <?php if ($n['link']): ?>
                        <a href="<?= htmlspecialchars($n['link']) ?>" class="notification-link">
                            ➡️ Lihat Detail
                        </a>
                    <?php endif; ?>
                    <?php if (!$n['is_read']): ?>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="notif_id" value="<?= $n['id'] ?>">
                            <button type="submit" class="mark-read-btn">Tandai Dibaca</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-notif">
            <p>📭 Tidak ada notifikasi.</p>
            <p>Semua notifikasi akan muncul di sini saat ada aktivitas baru.</p>
        </div>
    <?php endif; ?>

    <a href="dashboard.php" class="back-link">← Kembali ke Dashboard</a>
</div>

<?php include 'includes\footer.php'; ?>