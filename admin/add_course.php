<?php
require_once '../includes/header.php';
require_role(['admin']);

if ($_POST) {
    $kode = trim($_POST['kode_mk']);
    $nama = trim($_POST['nama_mk']);
    $sks = (int)$_POST['sks'];
    $deskripsi = trim($_POST['deskripsi']);

    if (empty($kode) || empty($nama) || $sks <= 0) {
        die("Kode, nama, dan SKS wajib diisi.");
    }

    $stmt = $pdo->prepare("INSERT INTO courses (kode_mk, nama_mk, sks, deskripsi) VALUES (?, ?, ?, ?)");
    $stmt->execute([$kode, $nama, $sks, $deskripsi]);

    header("Location: manage_courses.php?success=1");
    exit;
}
?>

<!-- Tambahkan CSS form -->
<link rel="stylesheet" href="/elearning/assets/css/form.css">

<h2>➕ Tambah Mata Kuliah Baru</h2>

<?php if (isset($_GET['success'])): ?>
    <div class="form-success">Mata kuliah berhasil ditambahkan.</div>
<?php endif; ?>

<div class="form-container">
    <form method="post">
        <div class="form-group">
            <label class="form-label">Kode Mata Kuliah:</label>
            <input type="text" name="kode_mk" class="form-input" placeholder="Contoh: INF202" required>
        </div>

        <div class="form-group">
            <label class="form-label">Nama Mata Kuliah:</label>
            <input type="text" name="nama_mk" class="form-input" placeholder="Contoh: Pemrograman Web" required>
        </div>

        <div class="form-group">
            <label class="form-label">SKS:</label>
            <select name="sks" class="form-select" required>
                <option value="">-- Pilih SKS --</option>
                <option value="1">1 SKS</option>
                <option value="2">2 SKS</option>
                <option value="3">3 SKS</option>
                <option value="4">4 SKS</option>
                <option value="5">5 SKS</option>
                <option value="6">6 SKS</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi (Opsional):</label>
            <textarea name="deskripsi" class="form-input" rows="4" placeholder="Deskripsi singkat mata kuliah..."></textarea>
        </div>

        <div class="form-btns">
            <button type="submit" class="btn btn-primary">Simpan Mata Kuliah</button>
            <a href="manage_courses.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>