<?php
require_once '../includes/header.php';
require_role(['admin']);

// Ambil semua pengguna
$stmt = $pdo->prepare("SELECT * FROM users ORDER BY role, nama_lengkap");
$stmt->execute();
$users = $stmt->fetchAll();
?>

<h2>👥 Manajemen Pengguna</h2>
<a href="add_user.php" class="btn">+ Tambah Pengguna</a>

<table class="table">
    <thead><tr><th>ID</th><th>Username</th><th>Nama Lengkap</th><th>Email</th><th>Role</th><th>Aksi</th></tr></thead>
    <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['nama_lengkap']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= ucfirst($u['role']) ?></td>
            <td>
                <a href="edit_user.php?id=<?= $u['id'] ?>">✏️ Edit</a> |
                <a href="delete_user.php?id=<?= $u['id'] ?>" onclick="return confirm('Hapus pengguna ini?')">🗑️ Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>