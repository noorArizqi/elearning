<?php 
require_once '../includes/header.php'; 
require_role(['mahasiswa']); 

// Ambil kelas yang diikuti
$stmt = $pdo->prepare("
    SELECT c.id, co.nama_mk, co.kode_mk, u.nama_lengkap AS dosen
    FROM enrollments e
    JOIN classes c ON e.id_class = c.id
    JOIN courses co ON c.id_course = co.id
    JOIN users u ON c.id_dosen = u.id
    WHERE e.id_mahasiswa = ?
");
$stmt->execute([$_SESSION['user_id']]);
$classes = $stmt->fetchAll();
?>

<h2>🎓 Dashboard Mahasiswa</h2>

<!-- Menu Cepat -->
<div class="quick-links" style="margin-bottom: 20px;">
    <a href="view_attendance.php" class="btn">📅 Lihat Absensi</a>
    <a href="grades.php" class="btn">📊 Rekap Nilai</a>
    <a href="export_grades.php" class="btn" style="background:#27ae60;">📤 Export Nilai (CSV)</a>
</div>

<h3>Kelas yang Diikuti</h3>

<?php if ($classes): ?>
    <div class="class-list">
        <?php foreach ($classes as $c): ?>
        <div class="class-item">
            <h3><?= htmlspecialchars($c['kode_mk']) ?> - <?= htmlspecialchars($c['nama_mk']) ?></h3>
            <p><strong>Dosen:</strong> <?= htmlspecialchars($c['dosen']) ?></p>
            <a href="view_class.php?id=<?= $c['id'] ?>" class="btn">➡️ Masuk Kelas</a>
            <a href="forum/view_forum.php?id=<?= $c['id'] ?>" class="btn" style="background:#9b59b6;">💬 Forum Kelas</a>
        </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert">
        <p>Anda belum terdaftar di kelas manapun.</p>
        <p>Silakan hubungi admin untuk pendaftaran kelas.</p>
    </div>
<?php endif; ?>

<style>
.quick-links {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.btn {
    display: inline-block;
    padding: 8px 16px;
    background: #eca20cff;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-size: 14px;
}
.btn:hover {
    background: #da1803ff;
}
.alert {
    padding: 15px;
    background: #fff8e1;
    border-left: 4px solid #ffc107;
    margin: 20px 0;
}
</style>

<?php include '../includes/footer.php'; ?>