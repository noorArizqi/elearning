<?php
require_once '../includes/header.php';
require_role(['mahasiswa']);
require_once '../includes/functions.php';

$class_id = $_GET['id'] ?? 0;
if (!is_mahasiswa_in_class($pdo, $_SESSION['user_id'], $class_id)) {
    die("Anda tidak terdaftar di kelas ini.");
}

// Info kelas
$stmt = $pdo->prepare("
    SELECT co.nama_mk, co.kode_mk, u.nama_lengkap AS dosen
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
$tugas = $pdo->prepare("
    SELECT a.*, s.file_tugas, s.waktu_upload, s.nilai
    FROM assignments a
    LEFT JOIN submissions s ON a.id = s.id_assignment AND s.id_mahasiswa = ?
    WHERE a.id_class = ?
    ORDER BY a.deadline DESC
");
$tugas->execute([$_SESSION['user_id'], $class_id]);

// Ambil pertemuan (untuk absensi)
$meetings = $pdo->prepare("
    SELECT m.*, c.id AS class_id
    FROM meetings m
    JOIN classes c ON m.id_class = c.id
    WHERE c.id = ? AND m.tanggal <= CURDATE()
    ORDER BY m.tanggal DESC
");
$meetings->execute([$class_id]);
?>

<h2>Kelas: <?= htmlspecialchars($class['kode_mk']) ?> - <?= htmlspecialchars($class['nama_mk']) ?></h2>
<p>Dosen: <?= htmlspecialchars($class['dosen']) ?></p>

<!-- Materi -->
<h3>📁 Materi Perkuliahan</h3>
<ul>
    <?php while ($m = $materi->fetch()): ?>
    <li>
        <strong><?= htmlspecialchars($m['judul']) ?></strong>
        <?php if ($m['tipe'] == 'file'): ?>
            - <a href="/elearning/assets/uploads/<?= htmlspecialchars($m['konten']) ?>" target="_blank">Unduh</a>
        <?php else: ?>
            - <a href="<?= htmlspecialchars($m['konten']) ?>" target="_blank">Lihat</a>
        <?php endif; ?>
    </li>
    <?php endwhile; ?>
</ul>

<!-- Tugas -->
<h3>📝 Tugas</h3>
<table class="table">
    <thead><tr><th>Judul</th><th>Deadline</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
        <?php while ($t = $tugas->fetch()): ?>
        <tr>
            <td><?= htmlspecialchars($t['judul']) ?></td>
            <td><?= date('d M Y H:i', strtotime($t['deadline'])) ?></td>
            <td>
                <?php if ($t['file_tugas']): ?>
                    <span style="color:green;">✅ Sudah dikumpulkan</span>
                    <?php if ($t['nilai'] !== null): ?>
                        | Nilai: <strong><?= $t['nilai'] ?>/<?= $t['skor_maksimal'] ?></strong>
                    <?php endif; ?>
                <?php else: ?>
                    <span style="color:red;">❌ Belum dikumpulkan</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if (!$t['file_tugas'] && strtotime($t['deadline']) >= time()): ?>
                    <a href="submit_assignment.php?assignment_id=<?= $t['id'] ?>">Kumpulkan</a>
                <?php elseif (strtotime($t['deadline']) < time()): ?>
                    <em>Telah ditutup</em>
                <?php else: ?>
                    <em>Sudah dikumpulkan</em>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Absensi -->
<h3>✅ Absensi</h3>
<?php if ($meetings->rowCount() > 0): ?>
    <p>Berikut pertemuan yang telah berlangsung:</p>
    <ul>
        <?php while ($mt = $meetings->fetch()): ?>
        <li>
            <strong>Pertemuan <?= $mt['pertemuan_ke'] ?></strong> (<?= $mt['tanggal'] ?>) — 
            <?php
            // Cek status absensi mahasiswa
            $stmt = $pdo->prepare("SELECT status FROM attendance WHERE id_meeting = ? AND id_mahasiswa = ?");
            $stmt->execute([$mt['id'], $_SESSION['user_id']]);
            $absen = $stmt->fetch();
            $status = $absen['status'] ?? 'alfa';
            $color = [
                'hadir' => 'green',
                'sakit' => 'orange',
                'izin' => 'blue',
                'alfa' => 'red'
            ];
            echo "<span style='color:{$color[$status]};'>".ucfirst($status)."</span>";
            ?>
            <br>
            <a href="scan_absen.php?meeting_id=<?= $mt['id'] ?>">Scan QR / Absen Langsung</a>
        </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>Belum ada pertemuan yang dibuka untuk absensi.</p>
<?php endif; ?>

<!-- Kuis -->
<h3>🧠 Kuis</h3>
<?php
// Ambil daftar kuis aktif
$quizzes = $pdo->prepare("
    SELECT q.*, c.id AS class_id
    FROM quizzes q
    JOIN classes c ON q.id_class = c.id
    WHERE c.id = ? AND q.created_at <= NOW()
    ORDER BY q.created_at DESC
");
$quizzes->execute([$class_id]);
?>

<?php if ($quizzes->rowCount() > 0): ?>
    <ul>
        <?php while ($q = $quizzes->fetch()): ?>
        <li>
            <strong><?= htmlspecialchars($q['judul']) ?></strong> 
            (<?= $q['durasi'] ?> menit)
            <br>
            <a href="take_quiz.php?id=<?= $q['id'] ?>">➡️ Kerjakan Kuis</a>
        </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>Belum ada kuis untuk kelas ini.</p>
<?php endif; ?>


<?php include '../includes/footer.php'; ?>