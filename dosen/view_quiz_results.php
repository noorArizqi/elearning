<?php
require_once '../includes/header.php';
require_role(['dosen']);

$quiz_id = $_GET['quiz_id'] ?? 0;

// Ambil info kuis + hitung jumlah soal
$stmt = $pdo->prepare("
    SELECT q.*, co.nama_mk,
           (SELECT COUNT(*) FROM questions WHERE id_quiz = q.id) as jumlah_soal
    FROM quizzes q
    JOIN classes c ON q.id_class = c.id
    JOIN courses co ON c.id_course = co.id
    WHERE q.id = ? AND c.id_dosen = ?
");
$stmt->execute([$quiz_id, $_SESSION['user_id']]);
$quiz = $stmt->fetch();

if (!$quiz) {
    die("Kuis tidak ditemukan.");
}

// Ambil hasil kuis
$results = $pdo->prepare("
    SELECT u.nama_lengkap, COUNT(a.id) as skor
    FROM users u
    LEFT JOIN quiz_attempts a ON u.id = a.id_mahasiswa AND a.id_quiz = ? AND a.benar = 1
    WHERE u.role = 'mahasiswa'
    GROUP BY u.id, u.nama_lengkap
");
$results->execute([$quiz_id]);
?>

<h2>📊 Hasil Kuis: <?= htmlspecialchars($quiz['judul']) ?></h2>
<p><strong>Kelas:</strong> <?= htmlspecialchars($quiz['nama_mk']) ?></p>

<table class="table">
    <thead><tr><th>Mahasiswa</th><th>Skor</th><th>Persentase</th></tr></thead>
    <tbody>
        <?php while ($r = $results->fetch()): ?>
        <?php
        $jumlah_soal = (int)$quiz['jumlah_soal'];
        $skor = (int)$r['skor'];
        $persentase = ($jumlah_soal > 0) ? round(($skor / $jumlah_soal) * 100, 2) : 0;
        ?>
        <tr>
            <td><?= htmlspecialchars($r['nama_lengkap']) ?></td>
            <td><?= $skor ?>/<?= $jumlah_soal ?></td>
            <td><?= $persentase ?>%</td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<a href="manage_class.php?id=<?= $quiz['id_class'] ?>">← Kembali ke Kelas</a>

<?php include '../includes/footer.php'; ?>