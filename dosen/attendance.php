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

<style>


/* Buttons */
.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.btn {
    flex: 1;
    padding: 0.75rem 1.25rem;
    border: none;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
    text-decoration: none;
    display: inline-block;
}
.btn-primary {
    background-color: #4f46e5;
    color: white;
}
.btn-primary:hover {
    background-color: #4338ca;
}
.btn-secondary {
    background-color: #f1f5f9;
    color: #334155;
}
.btn-secondary:hover {
    background-color: #e2e8f0;
}
</style>

<div class="page-container">
    <h2 class="page-title">📅 Buat Pertemuan Baru</h2>

    <?php if (!empty($error)): ?>
        <div class="alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="form-card">
        <form method="post">
            <div class="form-group">
                <label class="form-label">Pertemuan ke:</label>
                <input 
                    type="number" 
                    name="pertemuan" 
                    min="1" 
                    max="16" 
                    class="form-input" 
                    placeholder="Contoh: 1" 
                    required
                >
                <span class="form-hint">Nomor pertemuan (1–16)</span>
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal:</label>
                <input 
                    type="date" 
                    name="tanggal" 
                    class="form-input" 
                    value="<?= date('Y-m-d') ?>" 
                    required
                >
            </div>

            <div class="form-group">
                <label class="form-label">Topik:</label>
                <input 
                    type="text" 
                    name="topik" 
                    class="form-input" 
                    placeholder="Contoh: Pengenalan HTML & CSS" 
                    required
                >
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Buat & Generate QR</button>
                <a href="manage_class.php?id=<?= $class_id ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php include '../includes/footer.php'; ?>



