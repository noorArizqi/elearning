<?php
require_once '../includes/header.php';
require_role(['admin']);

// Ambil semua pengguna
$stmt = $pdo->prepare("SELECT * FROM users ORDER BY role, nama_lengkap");
$stmt->execute();
$users = $stmt->fetchAll();
?>

<style>
    .container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 10px;
    }
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 20px 0 30px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .page-title {
        font-size: 24px;
        color: #2c3e50;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-add {
        background: #0044ff;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-add:hover {
        background: #0033cc;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,68,255,0.2);
    }

    .table-container {
        overflow-x: auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        padding: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 16px;
    }

    thead th {
        background: #041fbbff;
        color: #fff;
        font-weight: 600;
        padding: 14px 12px;
        text-align: left;
        border-bottom: 2px solid #e0e6ff;
    }

    tbody td {
        padding: 14px 12px;
        border-bottom: 4px solid #eee;
        color: #333;
        vertical-align: top;
    }

    tbody tr:hover {
        background: #a5c1ffff;
    }

    .action-links a {
        margin-right: 12px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: color 0.2s;
    }

    .action-links a.edit {
        color: #0044ff;
    }

    .action-links a.edit:hover {
        color: #0033cc;
    }

    .action-links a.delete {
        color: #e74c3c;
    }

    .action-links a.delete:hover {
        color: #c0392b;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: stretch;
        }

        .table-container {
            padding: 15px;
        }

        thead th,
        tbody td {
            padding: 12px 8px;
            font-size: 13px;
        }

        .action-links a {
            display: block;
            margin: 4px 0;
        }
    }
</style>

<div class="container">
    <div class="page-header">
        <h2 class="page-title">👥 Manajemen Pengguna</h2>
        <a href="add_user.php" class="btn-add">➕ Tambah Pengguna</a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['nama_lengkap']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <span class="role-badge role-<?= $u['role'] ?>">
                            <?= ucfirst($u['role']) ?>
                        </span>
                    </td>
                    <td class="action-links">
                        <a href="edit_user.php?id=<?= $u['id'] ?>" class="edit">✏️ Edit</a>
                        <a href="delete_user.php?id=<?= $u['id'] ?>" 
                            class="delete"
                            onclick="return confirm('Anda yakin ingin menghapus pengguna ini?')">
                            🗑️ Hapus
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>