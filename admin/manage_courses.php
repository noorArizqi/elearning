<?php
require_once '../includes/header.php';
require_role(['admin']);

$stmt = $pdo->prepare("SELECT * FROM courses ORDER BY nama_mk");
$stmt->execute();
$courses = $stmt->fetchAll();
?>

<h2>📚 Manajemen Mata Kuliah</h2>
<a href="add_course.php" class="btn">+ Tambah Mata Kuliah</a>

<table class="table">
    <thead><tr><th>Kode MK</th><th>Nama MK</th><th>SKS</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
    <tbody>
        <?php foreach ($courses as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['kode_mk']) ?></td>
            <td><?= htmlspecialchars($c['nama_mk']) ?></td>
            <td><?= $c['sks'] ?></td>
            <td><?= htmlspecialchars($c['deskripsi']) ?></td>
            <td>
                <a href="edit_course.php?id=<?= $c['id'] ?>">✏️ Edit</a> |
                <a href="delete_course.php?id=<?= $c['id'] ?>" onclick="return confirm('Hapus mata kuliah ini?')">🗑️ Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>