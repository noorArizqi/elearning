<?php
require_once '../includes/header.php';
require_role(['admin']);

// Ambil daftar kelas
$stmt = $pdo->prepare("
    SELECT c.id, co.nama_mk, u.nama_lengkap AS dosen
    FROM classes c
    JOIN courses co ON c.id_course = co.id
    JOIN users u ON c.id_dosen = u.id
    ORDER BY co.nama_mk
");
$stmt->execute();
$classes = $stmt->fetchAll();

// Ambil daftar mahasiswa
$stmt = $pdo->prepare("SELECT id, nama_lengkap FROM users WHERE role = 'mahasiswa' ORDER BY nama_lengkap");
$stmt->execute();
$mahasiswas = $stmt->fetchAll();

if ($_POST) {
    $class_id = $_POST['id_class'];
    $student_id = $_POST['id_mahasiswa'];

    // Validasi
    if (!$class_id || !$student_id) {
        die("Kelas dan mahasiswa wajib dipilih.");
    }

    // Cek apakah sudah terdaftar
    $stmt = $pdo->prepare("SELECT 1 FROM enrollments WHERE id_class = ? AND id_mahasiswa = ?");
    $stmt->execute([$class_id, $student_id]);
    if ($stmt->fetch()) {
        die("Mahasiswa sudah terdaftar di kelas ini.");
    }

    $stmt = $pdo->prepare("INSERT INTO enrollments (id_class, id_mahasiswa) VALUES (?, ?)");
    $stmt->execute([$class_id, $student_id]);

    header("Location: enroll_students.php?success=1");
    exit;
}
?>

<!-- Tambahkan CSS form -->
<link rel="stylesheet" href="/elearning/assets/css/form.css">

<h2>👥 Daftarkan Mahasiswa ke Kelas</h2>

<?php if (isset($_GET['success'])): ?>
    <div class="form-success">Mahasiswa berhasil didaftarkan ke kelas.</div>
<?php endif; ?>

<div class="form-container">
    <form method="post">
        <div class="form-group">
            <label class="form-label">Kelas:</label>
            <?php if (empty($classes)): ?>
                <div class="form-input" style="background:#f8d7da; color:#721c24;">Belum ada kelas tersedia. <a href="create_class.php" style="color:#c0392b;">Buat Kelas</a></div>
            <?php else: ?>
                <select name="id_class" class="form-select" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($classes as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nama_mk']) ?> - <?= htmlspecialchars($c['dosen']) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label class="form-label">Mahasiswa:</label>
            <?php if (empty($mahasiswas)): ?>
                <div class="form-input" style="background:#f8d7da; color:#721c24;">Belum ada mahasiswa terdaftar. <a href="add_user.php" style="color:#c0392b;">Tambah Mahasiswa</a></div>
            <?php else: ?>
                <select name="id_mahasiswa" class="form-select" required>
                    <option value="">-- Pilih Mahasiswa --</option>
                    <?php foreach ($mahasiswas as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nama_lengkap']) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>

        <div class="form-btns">
            <?php if (!empty($classes) && !empty($mahasiswas)): ?>
                <button type="submit" class="btn btn-primary">Daftarkan ke Kelas</button>
            <?php endif; ?>
            <a href="dashboard.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>