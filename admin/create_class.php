<?php
require_once '../includes/header.php';
require_role(['admin']);

// Ambil daftar mata kuliah
$stmt = $pdo->prepare("SELECT * FROM courses ORDER BY nama_mk");
$stmt->execute();
$courses = $stmt->fetchAll();

// Ambil daftar dosen
$stmt = $pdo->prepare("SELECT id, nama_lengkap FROM users WHERE role = 'dosen' ORDER BY nama_lengkap");
$stmt->execute();
$dosens = $stmt->fetchAll();

if ($_POST) {
    $course_id = $_POST['id_course'];
    $dosen_id = $_POST['id_dosen'];
    $tahun = $_POST['tahun_akademik'];
    $semester = $_POST['semester'];

    // Validasi
    if (!$course_id || !$dosen_id || !$tahun || !$semester) {
        die("Semua field wajib diisi.");
    }

    $stmt = $pdo->prepare("INSERT INTO classes (id_course, id_dosen, tahun_akademik, semester) VALUES (?, ?, ?, ?)");
    $stmt->execute([$course_id, $dosen_id, $tahun, $semester]);

    header("Location: manage_classes.php?success=1");
    exit;
}
?>

<!-- Tambahkan CSS form -->
<link rel="stylesheet" href="/elearning/assets/css/form.css">

<h2>➕ Buat Kelas Baru</h2>

<?php if (isset($_GET['success'])): ?>
    <div class="form-success">Kelas berhasil dibuat.</div>
<?php endif; ?>

<div class="form-container">
    <form method="post">
        <div class="form-group">
            <label class="form-label">Mata Kuliah:</label>
            <select name="id_course" class="form-select" required>
                <option value="">-- Pilih Mata Kuliah --</option>
                <?php foreach ($courses as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nama_mk']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Dosen Pengampu:</label>
            <select name="id_dosen" class="form-select" required>
                <option value="">-- Pilih Dosen --</option>
                <?php foreach ($dosens as $d): ?>
                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nama_lengkap']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Tahun Akademik:</label>
            <input type="number" name="tahun_akademik" value="<?= date('Y') ?>" min="2020" max="2030" class="form-input" required>
        </div>

        <div class="form-group">
            <label class="form-label">Semester:</label>
            <select name="semester" class="form-select" required>
                <option value="Ganjil">Ganjil</option>
                <option value="Genap">Genap</option>
            </select>
        </div>

        <div class="form-btns">
            <button type="submit" class="btn btn-primary">Buat Kelas</button>
            <a href="manage_classes.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>