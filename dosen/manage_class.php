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

<h2>Kelola Kelas: <?= htmlspecialchars($class['kode_mk']) ?> - <?= htmlspecialchars($class['nama_mk']) ?></h2>

<nav class="tabs">
    <a href="#materi">📁 Materi</a> |
    <a href="#tugas">📝 Tugas</a> |
    <a href="#kuis">🧠 Kuis</a> |
    <a href="#absensi">✅ Absensi</a> |
    <a href="forum/view_forum.php?id=<?= $class_id ?>">💬 Forum</a> |
    <a href="../dashboard.php">🏠 Dashboard</a>
</nav>

<!-- Materi -->
<div id="materi">
    <h3>Materi Perkuliahan</h3>
    <a href="upload_material.php?class_id=<?= $class_id ?>" class="btn">+ Upload Materi</a>
    <ul>
        <?php while ($m = $materi->fetch()): ?>
        <li>
            <strong><?= htmlspecialchars($m['judul']) ?></strong> 
            (<?= $m['tipe'] ?>)
            <?php if ($m['tipe'] == 'file'): ?>
                - <a href="/elearning/assets/uploads/<?= htmlspecialchars($m['konten']) ?>" target="_blank">Lihat</a>
            <?php else: ?>
                - <a href="<?= htmlspecialchars($m['konten']) ?>" target="_blank">Buka Link</a>
            <?php endif; ?>
        </li>
        <?php endwhile; ?>
    </ul>
</div>

<!-- Tugas -->
<div id="tugas">
    <h3>Daftar Tugas</h3>
    <a href="create_assignment.php?class_id=<?= $class_id ?>" class="btn">+ Buat Tugas</a>
    <table class="table">
        <thead><tr><th>Judul</th><th>Deadline</th><th>Skor Max</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php while ($t = $tugas->fetch()): ?>
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
<div id="kuis">
    <h3>🧠 Kuis</h3>
    <a href="create_quiz.php?class_id=<?= $class_id ?>" class="btn">+ Buat Kuis</a>
    
    <!-- Di dalam div #kuis -->
    <?php
    // Ambil daftar kuis - gunakan id jika created_at belum ada
    $quizzes = $pdo->prepare("SELECT * FROM quizzes WHERE id_class = ? ORDER BY id DESC");
    $quizzes->execute([$class_id]);
    ?>
    
    <?php if ($quizzes->rowCount() > 0): ?>
        <table class="table">
            <thead><tr><th>Judul</th><th>Durasi</th><th>Aksi</th></tr></thead>
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
        <p>Belum ada kuis untuk kelas ini.</p>
    <?php endif; ?>
</div>

<!-- Absensi -->
<div id="absensi">
    <h3>Absensi</h3>
    <a href="attendance.php?class_id=<?= $class_id ?>" class="btn">+ Buat Pertemuan</a>
    <table class="table">
        <thead><tr><th>Pertemuan</th><th>Tanggal</th><th>Topik</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php while ($mt = $meetings->fetch()): ?>
            <tr>
                <td><?= $mt['pertemuan_ke'] ?></td>
                <td><?= $mt['tanggal'] ?></td>
                <td><?= htmlspecialchars($mt['topik']) ?></td>
                <td><a href="attendance_detail.php?meeting_id=<?= $mt['id'] ?>">Lihat Absen</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<style>
.btn {
    display: inline-block;
    background: #3498db;
    color: white;
    padding: 6px 12px;
    text-decoration: none;
    border-radius: 4px;
    margin-bottom: 10px;
}
.tabs { margin: 20px 0; }
</style>

<?php include '../includes/footer.php'; ?>