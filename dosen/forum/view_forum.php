<?php
require_once '../../includes/header.php';
require_role(['dosen']);
require_once '../../includes/functions.php';

$class_id = $_GET['id'] ?? 0;
if (!is_dosen_of_class($pdo, $_SESSION['user_id'], $class_id)) {
    die("Akses ditolak.");
}

// Ambil thread
$threads = $pdo->prepare("
    SELECT t.*, u.nama_lengkap, u.role 
    FROM forum_threads t
    JOIN users u ON t.id_user = u.id
    WHERE t.id_class = ?
    ORDER BY t.created_at DESC
");
$threads->execute([$class_id]);
?>

<h2>💬 Forum Diskusi Kelas</h2>

<a href="#" onclick="document.getElementById('new-thread').style.display='block'">+ Buat Thread Baru</a>

<div id="new-thread" style="display:none; margin:20px 0; padding:15px; border:1px solid #ddd; background:#f9f9f9;">
    <h3>Buat Thread</h3>
    <form method="post" action="post_thread.php">
        <input type="hidden" name="class_id" value="<?= $class_id ?>">
        Judul: <input type="text" name="judul" required><br><br>
        Isi: <textarea name="isi" rows="4" required></textarea><br><br>
        <button type="submit">Posting</button>
        <button type="button" onclick="this.closest('div').style.display='none'">Batal</button>
    </form>
</div>

<?php while ($t = $threads->fetch()): ?>
<div style="border-bottom:1px solid #eee; padding:15px 0;">
    <strong><?= htmlspecialchars($t['nama_lengkap']) ?> (<?= ucfirst($t['role']) ?>)</strong>
    <h4><?= htmlspecialchars($t['judul']) ?></h4>
    <p><?= nl2br(htmlspecialchars($t['isi'])) ?></p>
    <small><?= date('d M Y H:i', strtotime($t['created_at'])) ?></small>
    <br>
    <a href="view_replies.php?thread_id=<?= $t['id'] ?>">💬 Balas (<?= get_reply_count($pdo, $t['id']) ?>)</a>
</div>
<?php endwhile; ?>

<?php
function get_reply_count($pdo, $thread_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM forum_replies WHERE id_thread = ?");
    $stmt->execute([$thread_id]);
    return $stmt->fetchColumn();
}
include '../../includes/footer.php';
?>