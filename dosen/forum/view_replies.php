<?php
require_once '../../includes/header.php';
require_role(['dosen']);
require_once '../../includes/functions.php';

$thread_id = $_GET['thread_id'] ?? 0;

// Ambil info thread
$stmt = $pdo->prepare("
    SELECT t.*, u.nama_lengkap AS pembuat
    FROM forum_threads t
    JOIN users u ON t.id_user = u.id
    WHERE t.id = ?
");
$stmt->execute([$thread_id]);
$thread = $stmt->fetch();

if (!$thread) {
    die("Thread tidak ditemukan.");
}

// Ambil balasan
$replies = $pdo->prepare("
    SELECT r.*, u.nama_lengkap
    FROM forum_replies r
    JOIN users u ON r.id_user = u.id
    WHERE r.id_thread = ?
    ORDER BY r.created_at ASC
");
$replies->execute([$thread_id]);

// Ambil kelas untuk link kembali
$stmt = $pdo->prepare("SELECT id_class FROM forum_threads WHERE id = ?");
$stmt->execute([$thread_id]);
$class_id = $stmt->fetchColumn();
?>

<h2>💬 Balasan: <?= htmlspecialchars($thread['judul']) ?></h2>
<p><strong>Dibuat oleh:</strong> <?= htmlspecialchars($thread['pembuat']) ?></p>
<p><?= nl2br(htmlspecialchars($thread['isi'])) ?></p>

<hr>

<?php if ($replies->rowCount() > 0): ?>
    <h3>Balasan:</h3>
    <?php while ($r = $replies->fetch()): ?>
    <div style="margin:10px 0; padding:10px; background:#f9f9f9; border-left:4px solid #3498db;">
        <strong><?= htmlspecialchars($r['nama_lengkap']) ?></strong>
        <p><?= nl2br(htmlspecialchars($r['isi_komentar'])) ?></p>
        <small><?= date('d M Y H:i', strtotime($r['created_at'])) ?></small>
    </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>Belum ada balasan.</p>
<?php endif; ?>

<!-- Form Balas -->
<form method="post" action="post_reply.php">
    <input type="hidden" name="thread_id" value="<?= $thread_id ?>">
    <textarea name="isi_komentar" rows="4" placeholder="Tulis balasan..." required></textarea><br><br>
    <button type="submit">Kirim Balasan</button>
</form>

<a href="view_forum.php?id=<?= $class_id ?>">← Kembali ke Forum</a>

<?php include '../../includes/footer.php'; ?>