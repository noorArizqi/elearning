<?php
require_once '../includes/header.php';
require_role(['dosen']);
require_once '../includes/functions.php';

$assignment_id = $_GET['assignment_id'] ?? 0;

// Ambil info tugas
$stmt = $pdo->prepare("
    SELECT a.*, c.id AS class_id, co.nama_mk
    FROM assignments a
    JOIN classes c ON a.id_class = c.id
    JOIN courses co ON c.id_course = co.id
    WHERE a.id = ? AND c.id_dosen = ?
");
$stmt->execute([$assignment_id, $_SESSION['user_id']]);
$assignment = $stmt->fetch();

if (!$assignment) {
    die("<h2>❌ Tugas tidak ditemukan atau Anda tidak berhak mengaksesnya.</h2>");
}

// Ambil daftar pengumpulan
$stmt = $pdo->prepare("
    SELECT s.*, u.nama_lengkap, u.email
    FROM submissions s
    JOIN users u ON s.id_mahasiswa = u.id
    WHERE s.id_assignment = ?
    ORDER BY s.waktu_upload DESC
");
$stmt->execute([$assignment_id]);
$submissions = $stmt->fetchAll();
?>

<h2>📋 Pengumpulan Tugas: <?= htmlspecialchars($assignment['judul']) ?></h2>
<p><strong>Kelas:</strong> <?= htmlspecialchars($assignment['nama_mk']) ?></p>
<p><strong>Deadline:</strong> <?= date('d M Y H:i', strtotime($assignment['deadline'])) ?></p>
<p><strong>Skor Maksimal:</strong> <?= $assignment['skor_maksimal'] ?></p>

<table class="table">
    <thead>
        <tr>
            <th>No</th>
            <th>Mahasiswa</th>
            <th>File Tugas</th>
            <th>Waktu Upload</th>
            <th>Nilai</th>
            <th>Feedback</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($submissions as $s): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($s['nama_lengkap']) ?></td>
            <td>
                <?php if ($s['file_tugas']): ?>
                    <a href="/elearning/assets/uploads/tugas/<?= htmlspecialchars($s['file_tugas']) ?>" target="_blank">📥 Download</a>
                <?php else: ?>
                    <em>Tidak ada file</em>
                <?php endif; ?>
            </td>
            <td><?= date('d M Y H:i', strtotime($s['waktu_upload'])) ?></td>
            <td>
                <form method="post" style="display:inline;" onsubmit="return confirm('Simpan nilai?')">
                    <input type="hidden" name="submission_id" value="<?= $s['id'] ?>">
                    <input type="number" name="nilai" min="0" max="<?= $assignment['skor_maksimal'] ?>" value="<?= $s['nilai'] ?: '' ?>" step="0.5" style="width:60px;">
                </form>
            </td>
            <td>
                <textarea name="feedback" rows="2" style="width:100%;"><?= htmlspecialchars($s['feedback_dosen']) ?></textarea>
            </td>
            <td>
                <button type="submit" formaction="save_feedback.php">💾 Simpan</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="manage_class.php?id=<?= $assignment['class_id'] ?>">← Kembali ke Kelas</a>

<?php include '../includes/footer.php'; ?>