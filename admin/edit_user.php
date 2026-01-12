<?php
require_once '../includes/header.php';
require_role(['admin']);

$user_id = $_GET['id'] ?? 0;

// Ambil data user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    die("Pengguna tidak ditemukan.");
}

// Ambil data profil
$profile_stmt = $pdo->prepare("SELECT * FROM profiles WHERE id_user = ?");
$profile_stmt->execute([$user_id]);
$profile = $profile_stmt->fetch();

// Proses form jika POST
if ($_POST) {
    $nama = trim($_POST['nama_lengkap']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    // Validasi
    if (empty($nama) || empty($email)) {
        die("Nama lengkap dan email wajib diisi.");
    }

    // Update password jika diisi
    if (!empty($_POST['password'])) {
        $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET nama_lengkap = ?, email = ?, role = ?, password = ? WHERE id = ?");
        $stmt->execute([$nama, $email, $role, $hashed, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET nama_lengkap = ?, email = ?, role = ? WHERE id = ?");
        $stmt->execute([$nama, $email, $role, $user_id]);
    }

    // Simpan profil hanya untuk dosen/mahasiswa
    if ($role == 'dosen' || $role == 'mahasiswa') {
        $nim_nidn = trim($_POST['nim_nidn']);
        $fakultas = trim($_POST['fakultas']);
        $prodi = trim($_POST['prodi']);

        if ($profile) {
            $stmt = $pdo->prepare("UPDATE profiles SET nim_nidn = ?, fakultas = ?, prodi = ? WHERE id_user = ?");
            $stmt->execute([$nim_nidn, $fakultas, $prodi, $user_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO profiles (id_user, nim_nidn, fakultas, prodi) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $nim_nidn, $fakultas, $prodi]);
        }
    } elseif ($profile) {
        // Jika role berubah jadi admin, hapus profil
        $stmt = $pdo->prepare("DELETE FROM profiles WHERE id_user = ?");
        $stmt->execute([$user_id]);
    }

    header("Location: manage_users.php?success=1");
    exit;
}
?>

<!-- Tambahkan CSS form -->
<link rel="stylesheet" href="/elearning/assets/css/form.css">

<h2>✏️ Edit Pengguna: <?= htmlspecialchars($user['username']) ?></h2>

<?php if (isset($_GET['success'])): ?>
    <div class="form-success">Pengguna berhasil diperbarui.</div>
<?php endif; ?>

<div class="form-container">
    <form method="post" id="edit-user-form">
        <div class="form-group">
            <label class="form-label">Nama Lengkap:</label>
            <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" class="form-input" required>
        </div>

        <div class="form-group">
            <label class="form-label">Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-input" required>
        </div>

        <div class="form-group">
            <label class="form-label">Role:</label>
            <select name="role" class="form-select" required onchange="toggleProfilFields(this.value)">
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="dosen" <?= $user['role'] == 'dosen' ? 'selected' : '' ?>>Dosen</option>
                <option value="mahasiswa" <?= $user['role'] == 'mahasiswa' ? 'selected' : '' ?>>Mahasiswa</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Password (kosongkan jika tidak ingin ganti):</label>
            <input type="password" name="password" class="form-input">
        </div>

        <!-- Profil untuk Dosen & Mahasiswa -->
        <div id="profil-fields" style="<?= ($user['role'] == 'dosen' || $user['role'] == 'mahasiswa') ? 'display:block;' : 'display:none;' ?> margin-top:25px; padding:20px; background:#f8f9fa; border-radius:8px;">
            <h3 class="form-label" style="margin-top:0;">👤 Profil</h3>
            
            <div class="form-group">
                <label class="form-label">NIM/NIDN:</label>
                <input type="text" name="nim_nidn" value="<?= htmlspecialchars($profile['nim_nidn'] ?? '') ?>" class="form-input">
            </div>
            
            <div class="form-group">
                <label class="form-label">Fakultas:</label>
                <input type="text" name="fakultas" value="<?= htmlspecialchars($profile['fakultas'] ?? '') ?>" class="form-input">
            </div>
            
            <div class="form-group">
                <label class="form-label">Prodi:</label>
                <input type="text" name="prodi" value="<?= htmlspecialchars($profile['prodi'] ?? '') ?>" class="form-input">
            </div>
        </div>

        <div class="form-btns">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="manage_users.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<script>
function toggleProfilFields(role) {
    const profilFields = document.getElementById('profil-fields');
    if (role === 'dosen' || role === 'mahasiswa') {
        profilFields.style.display = 'block';
    } else {
        profilFields.style.display = 'none';
    }
}
</script>

<?php include '../includes/footer.php'; ?>