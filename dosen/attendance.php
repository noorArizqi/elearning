<?php
require_once '../includes/header.php';
require_role(['dosen']);
require_once '../includes/functions.php';

$class_id = $_GET['class_id'] ?? 0;
if (!is_dosen_of_class($pdo, $_SESSION['user_id'], $class_id)) {
    die("Akses ditolak.");
}

$error = '';

if ($_POST) {
    $pertemuan = (int)$_POST['pertemuan'];
    $tanggal = $_POST['tanggal'];
    $topik = trim($_POST['topik']);

    if ($pertemuan < 1 || $pertemuan > 16) {
        $error = "Nomor pertemuan harus antara 1-16.";
    } elseif (empty($tanggal)) {
        $error = "Tanggal wajib diisi.";
    } elseif (empty($topik)) {
        $error = "Topik wajib diisi.";
    } else {
        // Cek duplikat pertemuan
        $stmt = $pdo->prepare("SELECT 1 FROM meetings WHERE id_class = ? AND pertemuan_ke = ?");
        $stmt->execute([$class_id, $pertemuan]);
        if ($stmt->fetch()) {
            $error = "Pertemuan ke-$pertemuan sudah ada.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO meetings (id_class, pertemuan_ke, tanggal, topik) VALUES (?, ?, ?, ?)");
            $stmt->execute([$class_id, $pertemuan, $tanggal, $topik]);
            
            header("Location: attendance_qr.php?meeting_id=" . $pdo->lastInsertId());
            exit;
        }
    }
}
?>

<!-- Tambahkan CSS form -->
<link rel="stylesheet" href="/elearning/assets/css/form.css">

<h2>📅 Buat Pertemuan Baru</h2>

<?php if ($error): ?>
    <div style="background:#f8d7da; color:#721c24; padding:15px; margin:20px; border-radius:6px; text-align:center;"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="form-container">
    <form method="post">
        <div class="form-group">
            <label class="form-label">Pertemuan ke:</label>
            <input type="number" name="pertemuan" min="1" max="16" class="form-input" placeholder="Contoh: 1" required>
            <small style="color:#7f8c8d;">Nomor pertemuan (1-16)</small>
        </div>

        <div class="form-group">
            <label class="form-label">Tanggal:</label>
            <input type="date" name="tanggal" class="form-input" value="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Topik:</label>
            <input type="text" name="topik" class="form-input" placeholder="Contoh: Pengenalan HTML & CSS" required>
        </div>

        <div class="form-btns">
            <button type="submit" class="btn btn-primary">Buat & Generate QR</button>
            <a href="manage_class.php?id=<?= $class_id ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>