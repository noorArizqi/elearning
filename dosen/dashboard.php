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

<style>
    /* Container utama */
    .container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 0 15px;
    }

    /* Header halaman */
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

    /* Tombol Tambah */
    .btn-add {
        background: #0044ff;
        /* Biru ITESA */
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }

    .btn-add:hover {
        background: #0033cc;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 68, 255, 0.25);
    }

    /* Kontainer Tabel */
    .table-container {
        overflow-x: auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        padding: 20px;
    }

    /* Gaya Tabel */
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 15px;
    }

    thead th {
        background: #0044ff;
        /* 🔴 Diperbaiki: hapus 'ff' di akhir (bukan hex valid) */
        color: white;
        font-weight: 600;
        padding: 14px 12px;
        text-align: left;
        border-bottom: none;
    }

    tbody td {
        padding: 14px 12px;
        border-bottom: 1px solid #eee;
        /* 🔽 Diperkecil dari 4px */
        color: #333;
        vertical-align: top;
    }

    tbody tr:hover {
        background: #f0f7ff;
        /* 🔽 Lebih lembut dari #a5c1ffff */
    }

    /* Tautan Aksi */
    .action-links a {
        margin-right: 15px;
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
        text-decoration: underline;
    }

    .action-links a.delete {
        color: #e74c3c;
    }

    .action-links a.delete:hover {
        color: #c0392b;
        text-decoration: underline;
    }

    /* Responsif */
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
            font-size: 14px;
        }

        .action-links a {
            display: block;
            margin: 6px 0;
        }
    }
</style>

<div class="container">
    <div class="page-header">
        <h2 class="page-title">🎓 Dashboard Dosen</h2>
    </div>

    <h3 class="section-title">Kelas yang Diajar</h3>

    <?php if ($classes): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Kode MK</th>
                        <th>Mata Kuliah</th>
                        <th>Peserta</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($classes as $c): ?>
                        <tr>
                            <td><code><?= htmlspecialchars($c['kode_mk']) ?></code></td>
                            <td><?= htmlspecialchars($c['nama_mk']) ?></td>
                            <td><strong><?= (int)($c['peserta'] ?? 0) ?></strong></td>
                            <td class="action-links">
                                <a href="manage_class.php?id=<?= $c['id'] ?>" class="manage">📝 Kelola</a>
                                <a href="forum/view_forum.php?id=<?= $c['id'] ?>" class="forum">💬 Forum</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>Belum ada kelas yang Anda ampu.</p>
    <?php endif; ?>

    <?php include '../includes/footer.php'; ?>