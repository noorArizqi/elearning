<?php
require_once '../includes/header.php';
require_role(['mahasiswa']);

$assignment_id = $_GET['assignment_id'] ?? 0;

// Cek apakah tugas ada & belum lewat deadline
$stmt = $pdo->prepare("SELECT a.deadline, a.id_class, e.id_mahasiswa FROM assignments a JOIN enrollments e ON a.id_class = e.id_class WHERE a.id = ? AND e.id_mahasiswa = ?");
$stmt->execute([$assignment_id, $_SESSION['user_id']]);
$task = $stmt->fetch();

if (!$task) {
    die("<div style='text-align:center; padding:40px;'><h2>❌ Tugas tidak ditemukan</h2><p>Silakan kembali ke kelas.</p><a href='view_class.php?id=" . htmlspecialchars($task['id_class']) . "' style='color:#3498db;'>← Kembali ke Kelas</a></div>");
}

if (strtotime($task['deadline']) < time()) {
    die("<div style='text-align:center; padding:40px;'><h2>⏰ Batas Waktu Telah Lewat</h2><p>Tidak bisa mengumpulkan tugas.</p><a href='view_class.php?id=" . htmlspecialchars($task['id_class']) . "' style='color:#3498db;'>← Kembali ke Kelas</a></div>");
}

// Proses upload jika POST
if ($_POST && isset($_FILES['file'])) {
    $allowed = ['pdf', 'doc', 'docx', 'zip'];
    $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
    $max_size = 10 * 1024 * 1024; // 10MB

    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $error = "Upload gagal. Error: " . $_FILES['file']['error'];
    } elseif ($_FILES['file']['size'] > $max_size) {
        $error = "Ukuran file terlalu besar. Maksimal 10MB.";
    } elseif (!in_array($ext, $allowed)) {
        $error = "Format file tidak diizinkan. Hanya PDF/DOC/ZIP.";
    } else {
        $filename = uniqid() . '_' . basename($_FILES['file']['name']);
        $target_dir = __DIR__ . '/../../assets/uploads/tugas/';
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $target = $target_dir . $filename;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
            $stmt = $pdo->prepare("INSERT INTO submissions (id_assignment, id_mahasiswa, file_tugas) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE file_tugas = VALUES(file_tugas), waktu_upload = NOW()");
            $stmt->execute([$assignment_id, $_SESSION['user_id'], 'tugas/' . $filename]);

            header("Location: view_class.php?id=" . $task['id_class'] . "&success=1");
            exit;
        } else {
            $error = "Gagal menyimpan file. Pastikan folder uploads writable.";
        }
    }

    if (isset($error)) {
        echo "<div style='background:#f8d7da; color:#721c24; padding:15px; margin:20px; border-radius:6px; text-align:center;'>$error</div>";
    }
}
?>

<!-- Tambahkan CSS form -->
<link rel="stylesheet" href="/elearning/assets/css/form.css">

<h2>📤 Kumpulkan Tugas</h2>

<?php if (isset($_GET['success'])): ?>
    <div class="form-success">Tugas berhasil dikumpulkan!</div>
<?php endif; ?>

<div class="form-container">
    <form method="post" enctype="multipart/form-data" id="upload-form">
        <div class="form-group">
            <label class="form-label">Pilih File Tugas (PDF/DOC/ZIP):</label>
            <input type="file" name="file" id="file-input" accept=".pdf,.doc,.docx,.zip" class="form-input" required>
            <div id="file-preview" style="margin-top:10px; font-size:14px; color:#7f8c8d;"></div>
        </div>

        <div class="form-btns">
            <button type="submit" id="submit-btn" class="btn btn-primary" disabled>Kirim Tugas</button>
            <a href="javascript:history.back()" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<script>
document.getElementById('file-input').addEventListener('change', function() {
    const file = this.files[0];
    const preview = document.getElementById('file-preview');
    const submitBtn = document.getElementById('submit-btn');

    if (file) {
        const ext = file.name.split('.').pop().toLowerCase();
        const size = (file.size / 1024 / 1024).toFixed(2) + ' MB';

        preview.innerHTML = `
            <strong>📁 Nama File:</strong> ${file.name} <br>
            <strong>📏 Ukuran:</strong> ${size} <br>
            <strong>✅ Format:</strong> .${ext}
        `;

        // Aktifkan tombol jika format valid
        if (['pdf', 'doc', 'docx', 'zip'].includes(ext)) {
            submitBtn.disabled = false;
            submitBtn.style.opacity = 1;
        } else {
            submitBtn.disabled = true;
            submitBtn.style.opacity = 0.6;
            preview.innerHTML += '<br><span style="color:red;">⚠️ Format tidak didukung.</span>';
        }
    } else {
        preview.innerHTML = '';
        submitBtn.disabled = true;
        submitBtn.style.opacity = 0.6;
    }
});

// Tampilkan animasi loading saat submit
document.getElementById('upload-form').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.textContent = 'Mengirim...';
    submitBtn.disabled = true;
    submitBtn.style.opacity = 0.8;
});
</script>

<?php include '../includes/footer.php'; ?>