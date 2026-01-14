<?php
require_once '../includes/header.php';
require_role(['admin']);

if ($_POST) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $nama = trim($_POST['nama_lengkap']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    if (empty($username) || empty($password) || empty($nama) || empty($email)) {
        die("Semua field wajib diisi.");
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, email, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$username, $hashed, $nama, $email, $role]);

    $user_id = $pdo->lastInsertId();

    if ($role == 'dosen' || $role == 'mahasiswa') {
        $nim_nidn = trim($_POST['nim_nidn']);
        $fakultas = trim($_POST['fakultas']);
        $prodi = trim($_POST['prodi']);

        $stmt = $pdo->prepare("INSERT INTO profiles (id_user, nim_nidn, fakultas, prodi) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $nim_nidn, $fakultas, $prodi]);
    }

    header("Location: manage_users.php?success=1");
    exit;
}
?>
<!-- Tambahkan CSS form -->
<link rel="stylesheet" href="/elearning/assets/css/form.css">

<div class="container">
    <h2 class="page-title">➕ Tambah Pengguna Baru</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="form-success">✅ Pengguna berhasil ditambahkan.</div>
    <?php endif; ?>

    <div class="form-container">
        <form method="post">
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-input" required placeholder="Masukkan username unik">
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" required minlength="6" placeholder="Minimal 6 karakter">
            </div>

            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-input" required placeholder="Nama lengkap pengguna">
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" required placeholder="contoh@univ.ac.id">
            </div>

            <div class="form-group">
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="admin">Admin</option>
                    <option value="dosen">Dosen</option>
                    <option value="mahasiswa">Mahasiswa</option>
                </select>
            </div>

            <!-- Profil untuk Dosen & Mahasiswa -->
            <div id="profil-fields">
                <h3 class="form-label">👤 Data Profil Tambahan</h3>
                <div class="form-group">
                    <label class="form-label">NIM / NIDN</label>
                    <input type="text" name="nim_nidn" class="form-input" placeholder="Contoh: 23105123 atau 19870101">
                </div>
                <div class="form-group">
                    <label class="form-label">Fakultas</label>
                    <input type="text" name="fakultas" class="form-input" placeholder="Contoh: Ilmu Komputer">
                </div>
                <div class="form-group">
                    <label class="form-label">Program Studi</label>
                    <input type="text" name="prodi" class="form-input" placeholder="Contoh: Teknologi Informasi">
                </div>
            </div>

            <div class="form-btns">
                <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
                <a href="manage_users.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelector('select[name="role"]').addEventListener('change', function() {
    const profilFields = document.getElementById('profil-fields');
    if (this.value === 'dosen' || this.value === 'mahasiswa') {
        profilFields.style.display = 'block';
    } else {
        profilFields.style.display = 'none';
    }
});
</script>

<?php include '../includes/footer.php'; ?>