<?php
require_once '../includes/header.php';
require_role(['mahasiswa']);

// Hitung total nilai per kelas
$stmt = $pdo->prepare("
    SELECT 
        co.kode_mk,
        co.nama_mk,
        COALESCE(AVG(s.nilai), 0) AS rata_rata_nilai
    FROM classes c
    JOIN courses co ON c.id_course = co.id
    JOIN enrollments e ON c.id = e.id_class
    LEFT JOIN assignments a ON c.id = a.id_class
    LEFT JOIN submissions s ON a.id = s.id_assignment AND s.id_mahasiswa = e.id_mahasiswa
    WHERE e.id_mahasiswa = ?
    GROUP BY c.id, co.kode_mk, co.nama_mk
");
$stmt->execute([$_SESSION['user_id']]);
$grades = $stmt->fetchAll();
?>

<h2>📊 Rekap Nilai Saya</h2>
<table class="table">
    <thead><tr><th>Kode MK</th><th>Mata Kuliah</th><th>Rata-rata Nilai</th></tr></thead>
    <tbody>
        <?php foreach ($grades as $g): ?>
        <tr>
            <td><?= htmlspecialchars($g['kode_mk']) ?></td>
            <td><?= htmlspecialchars($g['nama_mk']) ?></td>
            <td>
                <?php if ($g['rata_rata_nilai'] > 0): ?>
                    <strong><?= number_format($g['rata_rata_nilai'], 2) ?></strong>
                <?php else: ?>
                    <em>Belum ada nilai</em>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>