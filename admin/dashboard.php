<?php
require_once '../includes/header.php';
require_role(['admin']);
?>

<h2>📊 Dashboard Admin</h2>

<div class="card-grid">
    <div class="card">
        <h3>👥 Manajemen Pengguna</h3>
        <p>Kelola akun Admin, Dosen, dan Mahasiswa.</p>
        <a href="manage_users.php">Lihat Semua Pengguna</a>
    </div>
    <div class="card">
        <h3>📚 Mata Kuliah</h3>
        <p>Buat dan kelola daftar mata kuliah.</p>
        <a href="manage_courses.php">Kelola MK</a>
    </div>
    <div class="card">
        <h3>🏫 Kelas</h3>
        <p>Buat kelas baru dan hubungkan dengan dosen & mata kuliah.</p>
        <a href="create_class.php">Buat Kelas</a>
    </div>
    <div class="card">
        <h3>🎓 Pendaftaran Mahasiswa</h3>
        <p>Daftarkan mahasiswa ke kelas yang tersedia.</p>
        <a href="enroll_students.php">Daftarkan Mahasiswa</a>
    </div>
    <div class="card">
        <h3>⚙️ Sistem</h3>
        <p>Pengaturan global aplikasi.</p>
        <a href="system_settings.php">Pengaturan</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>