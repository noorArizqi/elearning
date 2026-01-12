<?php
require_once '../includes/header.php';
require_role(['dosen']);
require_once '../includes/functions.php';

$class_id = $_GET['class_id'] ?? 0;
if (!is_dosen_of_class($pdo, $_SESSION['user_id'], $class_id)) {
    die("Akses ditolak.");
}

if ($_POST) {
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $deadline = $_POST['deadline']; // format: YYYY-MM-DDTHH:mm
    $skor = (int)$_POST['skor'];

    if (empty($judul)) {
        $error = "Judul tugas wajib diisi.";
    } elseif ($skor <= 0) {
        $error = "Skor maksimal harus lebih dari 0.";
    } elseif (strtotime($deadline) < time()) {
        $error = "Deadline tidak boleh di masa lalu.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO assignments (id_class, judul, deskripsi, deadline, skor_maksimal) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$class_id, $judul, $deskripsi, $deadline, $skor]);
        header("Location: manage_class.php?id=$class_id&success=1");
        exit;
    }
}
?>

<!-- Tambahkan CSS form -->
<link rel="stylesheet" href="/elearning/assets/css/form.css">

<h2>📝 Buat Tugas Baru</h2>

<?php if (isset($error)): ?>
    <div style="background:#f8d7da; color:#721c24; padding:15px; margin:20px; border-radius:6px; text-align:center;"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if (isset($_GET['success'])): ?>
    <div class="form-success">Tugas berhasil dibuat.</div>
<?php endif; ?>

<div class="form-container">
    <form method="post">
        <div class="form-group">
            <label class="form-label">Judul Tugas:</label>
            <input type="text" name="judul" class="form-input" placeholder="Contoh: Tugas HTML & CSS" required>
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi (Opsional):</label>
            <textarea name="deskripsi" class="form-input" rows="4" placeholder="Petunjuk pengerjaan tugas..."></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Deadline:</label>
            <input type="datetime-local" name="deadline" class="form-input" required>
        </div>

        <div class="form-group">
            <label class="form-label">Skor Maksimal:</label>
            <input type="number" name="skor" min="1" max="1000" value="100" class="form-input" required>
            <small style="color:#7f8c8d;">Nilai maksimum untuk tugas ini</small>
        </div>

        <div class="form-btns">
            <button type="submit" class="btn btn-primary">Buat Tugas</button>
            <a href="manage_class.php?id=<?= $class_id ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>