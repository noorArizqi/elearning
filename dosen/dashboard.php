<?php 
require_once '../includes/header.php'; 
require_role(['dosen']); 

// Ambil kelas yang diajar
$stmt = $pdo->prepare("
    SELECT c.id, co.nama_mk, co.kode_mk, COUNT(e.id_mahasiswa) AS peserta
    FROM classes c
    JOIN courses co ON c.id_course = co.id
    LEFT JOIN enrollments e ON c.id = e.id_class
    WHERE c.id_dosen = ?
    GROUP BY c.id
");
$stmt->execute([$_SESSION['user_id']]);
$classes = $stmt->fetchAll();
?>

<h2>🎓 Dashboard Dosen</h2>
<h3>Kelas yang Diajar</h3>

<?php if ($classes): ?>
    <table class="table">
        <thead>
            <tr><th>Kode MK</th><th>Mata Kuliah</th><th>Peserta</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            <?php foreach ($classes as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['kode_mk']) ?></td>
                <td><?= htmlspecialchars($c['nama_mk']) ?></td>
                <td><?= $c['peserta'] ?? 0 ?></td>
                <td>
                    <a href="manage_class.php?id=<?= $c['id'] ?>">Kelola</a> |
                    <a href="forum/view_forum.php?id=<?= $c['id'] ?>">💬 Forum</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Belum ada kelas yang Anda ampu.</p>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>