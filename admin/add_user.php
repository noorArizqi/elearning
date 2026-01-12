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

<!-- Tambahkan CSS -->
<link rel="stylesheet" href="/elearning/assets/css/form.css">

<h2>➕ Tambah Pengguna Baru</h2>

<?php if (isset($_GET['success'])): ?>
    <div class="form-success">Pengguna berhasil ditambahkan.</div>
<?php endif; ?>

<div class="form-container">
    <form method="post">
        <div class="form-group">
            <label class="form-label">Username:</label>
            <input type="text" name="username" class="form-input" required>
        </div>

        <div class="form-group">
            <label class="form-label">Password:</label>
            <input type="password" name="password" class="form-input" required>
        </div>

        <div class="form-group">
            <label class="form-label">Nama Lengkap:</label>
            <input type="text" name="nama_lengkap" class="form-input" required>
        </div>

        <div class="form-group">
            <label class="form-label">Email:</label>
            <input type="email" name="email" class="form-input" required>
        </div>

        <div class="form-group">
            <label class="form-label">Role:</label>
            <select name="role" class="form-select" required>
                <option value="admin">Admin</option>
                <option value="dosen">Dosen</option>
                <option value="mahasiswa">Mahasiswa</option>
            </select>
        </div>

        <!-- Profil untuk Dosen & Mahasiswa -->
        <div id="profil-fields" style="display:none; margin-top:20px; padding:15px; background:#f8f9fa; border-radius:6px;">
            <h3 class="form-label">👤 Profil</h3>
            <div class="form-group">
                <label class="form-label">NIM/NIDN:</label>
                <input type="text" name="nim_nidn" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Fakultas:</label>
                <input type="text" name="fakultas" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Prodi:</label>
                <input type="text" name="prodi" class="form-input">
            </div>
        </div>

        <div class="form-btns">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="manage_users.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
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