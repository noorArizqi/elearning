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

<h2>🔔 Notifikasi Anda</h2>

<?php if ($notifications): ?>
    <ul style="list-style: none; padding: 0;">
        <?php foreach ($notifications as $n): ?>
        <li style="padding: 15px; margin: 10px 0; background: #f9f9f9; border-left: 4px solid <?= $n['is_read'] ? '#ccc' : '#3498db' ?>;">
            <strong><?= htmlspecialchars($n['pengirim']) ?></strong> 
            <p><?= htmlspecialchars($n['message']) ?></p>
            <small><?= date('d M Y H:i', strtotime($n['created_at'])) ?></small>
            <?php if ($n['link']): ?>
                <br><a href="<?= htmlspecialchars($n['link']) ?>">➡️ Lihat Detail</a>
            <?php endif; ?>
            <?php if (!$n['is_read']): ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="notif_id" value="<?= $n['id'] ?>">
                    <button type="submit" style="background:none; border:none; color:blue; text-decoration:underline;">Tandai Dibaca</button>
                </form>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Tidak ada notifikasi.</p>
<?php endif; ?>

<!-- Form tandai dibaca -->
<?php
if ($_POST && isset($_POST['notif_id'])) {
    $notif_id = $_POST['notif_id'];
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND id_user = ?");
    $stmt->execute([$notif_id, $_SESSION['user_id']]);
    header("Location: notifications.php");
    exit;
}
?>

<a href="dashboard.php">← Kembali ke Dashboard</a>

<?php include 'includes/footer.php'; ?>