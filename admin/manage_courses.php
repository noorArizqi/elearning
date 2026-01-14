<?php
require_once '../includes/header.php';
require_role(['admin']);

$stmt = $pdo->prepare("SELECT * FROM courses ORDER BY nama_mk");
$stmt->execute();
$courses = $stmt->fetchAll();
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
        <h2 class="page-title">📚 Manajemen Mata Kuliah</h2>
        <a href="add_course.php" class="btn-add">➕ Tambah Mata Kuliah</a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Kode MK</th>
                    <th>Nama Mata Kuliah</th>
                    <th>SKS</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $c): ?>
                <tr>
                    <td><code><?= htmlspecialchars($c['kode_mk']) ?></code></td>
                    <td><?= htmlspecialchars($c['nama_mk']) ?></td>
                    <td><strong><?= (int)$c['sks'] ?></strong></td>
                    <td><?= htmlspecialchars($c['deskripsi'] ?? '–') ?></td>
                    <td class="action-links">
                        <a href="edit_course.php?id=<?= $c['id'] ?>" class="edit">✏️ Edit</a>
                        <a href="delete_course.php?id=<?= $c['id'] ?>" 
                            class="delete"
                            onclick="return confirm('Anda yakin ingin menghapus mata kuliah ini?')">
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