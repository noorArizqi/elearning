<?php
require_once '../includes/header.php';
require_role(['admin']);

$course_id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course) {
    die("Mata kuliah tidak ditemukan.");
}

if ($_POST) {
    $nama = trim($_POST['nama_mk']);
    $sks = (int)$_POST['sks'];
    $deskripsi = trim($_POST['deskripsi']);

    if (empty($nama) || $sks <= 0) {
        die("Nama dan SKS wajib diisi.");
    }

    $stmt = $pdo->prepare("UPDATE courses SET nama_mk = ?, sks = ?, deskripsi = ? WHERE id = ?");
    $stmt->execute([$nama, $sks, $deskripsi, $course_id]);

    header("Location: manage_courses.php?success=1");
    exit;
}
?>

<!-- Tambahkan CSS form -->
<link rel="stylesheet" href="/elearning/assets/css/form.css">

<h2>✏️ Edit Mata Kuliah: <?= htmlspecialchars($course['kode_mk']) ?></h2>

<?php if (isset($_GET['success'])): ?>
    <div class="form-success">Mata kuliah berhasil diperbarui.</div>
<?php endif; ?>

<div class="form-container">
    <form method="post">
        <div class="form-group">
            <label class="form-label">Nama Mata Kuliah:</label>
            <input type="text" name="nama_mk" value="<?= htmlspecialchars($course['nama_mk']) ?>" class="form-input" required>
        </div>

        <div class="form-group">
            <label class="form-label">SKS:</label>
            <select name="sks" class="form-select" required>
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <option value="<?= $i ?>" <?= $i == $course['sks'] ? 'selected' : '' ?>><?= $i ?> SKS</option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi:</label>
            <textarea name="deskripsi" class="form-input" rows="4"><?= htmlspecialchars($course['deskripsi']) ?></textarea>
        </div>

        <div class="form-btns">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="manage_courses.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>