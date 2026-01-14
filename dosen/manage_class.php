<?php
require_once '../includes/header.php';
require_role(['dosen']);
require_once '../includes/functions.php';

$class_id = $_GET['id'] ?? 0;

if (!is_dosen_of_class($pdo, $_SESSION['user_id'], $class_id)) {
    die("Anda tidak berhak mengakses kelas ini.");
}

// Ambil info kelas
$stmt = $pdo->prepare("
    SELECT c.id, co.nama_mk, co.kode_mk, u.nama_lengkap AS dosen
    FROM classes c
    JOIN courses co ON c.id_course = co.id
    JOIN users u ON c.id_dosen = u.id
    WHERE c.id = ?
");
$stmt->execute([$class_id]);
$class = $stmt->fetch();

// Ambil materi
$materi = $pdo->prepare("SELECT * FROM materials WHERE id_class = ? ORDER BY urutan_tampil");
$materi->execute([$class_id]);

// Ambil tugas
$tugas = $pdo->prepare("SELECT * FROM assignments WHERE id_class = ? ORDER BY deadline DESC");
$tugas->execute([$class_id]);

// Ambil pertemuan
$meetings = $pdo->prepare("SELECT * FROM meetings WHERE id_class = ? ORDER BY pertemuan_ke");
$meetings->execute([$class_id]);
?>

<link rel="stylesheet" href="/elearning/assets/css/form.css">
<style>
/* Container */
.container {
    max-width: 900px;
    margin: 2rem auto;
    padding: 0 1.5rem;
}
</style>

<div class="container">
    <h1 class="page-header">Kelola Kelas: <?= htmlspecialchars($class['kode_mk']) ?> - <?= htmlspecialchars($class['nama_mk']) ?></h1>

    <!-- Tabs -->
    <nav class="tabs">
        <a href="#materi" class="tab-link active">📁 Materi</a>
        <a href="#tugas" class="tab-link">📝 Tugas</a>
        <a href="#kuis" class="tab-link">🧠 Kuis</a>
        <a href="#absensi" class="tab-link">✅ Absensi</a>
        <a href="forum/view_forum.php?id=<?= $class_id ?>" class="tab-link">💬 Forum</a>
        <a href="../dashboard.php" class="tab-link">🏠 Dashboard</a>
    </nav>

    <!-- Materi -->
    <div id="materi" class="section">
        <h3>📁 Materi Perkuliahan</h3>
        <a href="upload_material.php?class_id=<?= $class_id ?>" class="btn">+ Upload Materi</a>
        <ul class="material-list">
            <?php 
            // Reset pointer jika diperlukan (opsional)
            $materi->execute([$class_id]); 
            while ($m = $materi->fetch()): ?>
                <li>
                    <strong><?= htmlspecialchars($m['judul']) ?></strong>
                    <span class="type-badge"><?= ucfirst($m['tipe']) ?></span>
                    <?php if ($m['tipe'] == 'file'): ?>
                        <a href="/elearning/assets/uploads/<?= htmlspecialchars($m['konten']) ?>" target="_blank" rel="noopener">Lihat File</a>
                    <?php else: ?>
                        <a href="<?= htmlspecialchars($m['konten']) ?>" target="_blank" rel="noopener">Buka Link</a>
                    <?php endif; ?>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <!-- Tugas -->
    <div id="tugas" class="section">
        <h3>📝 Daftar Tugas</h3>
        <a href="create_assignment.php?class_id=<?= $class_id ?>" class="btn">+ Buat Tugas</a>
        <table class="table">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Deadline</th>
                    <th>Skor Max</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $tugas->execute([$class_id]);
                while ($t = $tugas->fetch()): ?>
                    <tr>
                        <td><?= htmlspecialchars($t['judul']) ?></td>
                        <td><?= date('d M Y H:i', strtotime($t['deadline'])) ?></td>
                        <td><?= $t['skor_maksimal'] ?></td>
                        <td>
                            <a href="view_submissions.php?assignment_id=<?= $t['id'] ?>">Lihat Pengumpulan</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Kuis -->
    <div id="kuis" class="section">
        <h3>🧠 Kuis</h3>
        <a href="create_quiz.php?class_id=<?= $class_id ?>" class="btn">+ Buat Kuis</a>
        
        <?php
        $quizzes = $pdo->prepare("SELECT * FROM quizzes WHERE id_class = ? ORDER BY id DESC");
        $quizzes->execute([$class_id]);
        if ($quizzes->rowCount() > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Durasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($q = $quizzes->fetch()): ?>
                        <tr>
                            <td><?= htmlspecialchars($q['judul']) ?></td>
                            <td><?= $q['durasi'] ?> menit</td>
                            <td>
                                <a href="view_quiz_results.php?quiz_id=<?= $q['id'] ?>">📊 Hasil</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="empty-state">Belum ada kuis untuk kelas ini.</p>
        <?php endif; ?>
    </div>

    <!-- Absensi -->
    <div id="absensi" class="section">
        <h3>✅ Absensi</h3>
        <a href="attendance.php?class_id=<?= $class_id ?>" class="btn">+ Buat Pertemuan</a>
        <table class="table">
            <thead>
                <tr>
                    <th>Pertemuan</th>
                    <th>Tanggal</th>
                    <th>Topik</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $meetings->execute([$class_id]);
                while ($mt = $meetings->fetch()): ?>
                    <tr>
                        <td><?= $mt['pertemuan_ke'] ?></td>
                        <td><?= date('d M Y', strtotime($mt['tanggal'])) ?></td>
                        <td><?= htmlspecialchars($mt['topik']) ?></td>
                        <td>
                            <a href="attendance_detail.php?meeting_id=<?= $mt['id'] ?>">Lihat Absen</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Tab Switching (Opsional - hanya scroll) -->
<script>
// Opsional: tambahkan highlight tab aktif berdasarkan hash
document.addEventListener('DOMContentLoaded', function() {
    const links = document.querySelectorAll('.tab-link');
    const hash = window.location.hash || '#materi';
    
    links.forEach(link => {
        if (link.getAttribute('href') === hash) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>