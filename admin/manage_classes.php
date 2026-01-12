<?php
require_once '../includes/header.php';
require_role(['admin']);

$stmt = $pdo->prepare("
    SELECT c.id, co.nama_mk, u.nama_lengkap AS dosen, c.tahun_akademik, c.semester
    FROM classes c
    JOIN courses co ON c.id_course = co.id
    JOIN users u ON c.id_dosen = u.id
    ORDER BY c.tahun_akademik DESC
");
$stmt->execute();
$classes = $stmt->fetchAll();
?>

<h2>📚 Manajemen Kelas</h2>
<a href="create_class.php" class="btn">+ Buat Kelas</a>

<table class="table">
    <thead>
        <tr>
            <th>Mata Kuliah</th>
            <th>Dosen</th>
            <th>Tahun</th>
            <th>Semester</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($classes as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['nama_mk']) ?></td>
            <td><?= htmlspecialchars($c['dosen']) ?></td>
            <td><?= $c['tahun_akademik'] ?></td>
            <td><?= $c['semester'] ?></td>
            <td>
                <a href="edit_class.php?id=<?= $c['id'] ?>">✏️ Edit</a> |
                <a href="delete_class.php?id=<?= $c['id'] ?>" onclick="return confirm('Hapus kelas ini?')">🗑️ Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>