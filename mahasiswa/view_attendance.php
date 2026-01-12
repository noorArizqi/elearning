<?php
require_once '../includes/header.php';
require_role(['mahasiswa']);
require_once '../includes/functions.php';

// Ambil semua pertemuan yang terdaftar di kelas mahasiswa
$stmt = $pdo->prepare("
    SELECT m.*, c.id AS class_id, co.nama_mk
    FROM meetings m
    JOIN classes c ON m.id_class = c.id
    JOIN courses co ON c.id_course = co.id
    JOIN enrollments e ON c.id = e.id_class
    WHERE e.id_mahasiswa = ?
    ORDER BY m.tanggal DESC
");
$stmt->execute([$_SESSION['user_id']]);
$meetings = $stmt->fetchAll();

// Ambil data absensi mahasiswa
$absen = $pdo->prepare("
    SELECT id_meeting, status, waktu_presensi
    FROM attendance
    WHERE id_mahasiswa = ?
");
$absen->execute([$_SESSION['user_id']]);
$absen_data = [];
while ($row = $absen->fetch()) {
    $absen_data[$row['id_meeting']] = $row;
}
?>

<h2>📅 Riwayat Absensi Saya</h2>

<?php if (!$meetings): ?>
    <p>Belum ada pertemuan di kelas Anda.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Kelas</th>
                <th>Pertemuan ke</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Waktu Presensi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($meetings as $m): ?>
            <tr>
                <td><?= htmlspecialchars($m['nama_mk']) ?></td>
                <td><?= $m['pertemuan_ke'] ?></td>
                <td><?= $m['tanggal'] ?></td>
                <td>
                    <?php
                    $status = $absen_data[$m['id']]['status'] ?? 'alfa';
                    $color = [
                        'hadir' => 'green',
                        'sakit' => 'orange',
                        'izin' => 'blue',
                        'alfa' => 'red'
                    ];
                    echo "<span style='color:{$color[$status]};'>".ucfirst($status)."</span>";
                    ?>
                </td>
                <td><?= $absen_data[$m['id']]['waktu_presensi'] ?? '-' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<a href="dashboard.php">← Kembali ke Dashboard</a>

<?php include '../includes/footer.php'; ?>