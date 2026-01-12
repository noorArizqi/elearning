<?php
require_once '../includes/header.php';
require_role(['dosen']);
require_once '../includes/functions.php';

$class_id = $_GET['class_id'] ?? 0;
if (!is_dosen_of_class($pdo, $_SESSION['user_id'], $class_id)) {
    die("Akses ditolak.");
}

$error = '';

if ($_POST) {
    $judul = trim($_POST['judul']);
    $tipe = $_POST['tipe'];
    $konten = '';

    if (empty($judul)) {
        $error = "Judul materi wajib diisi.";
    } elseif ($tipe === 'file') {
        // Validasi file
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $error = "Upload gagal. Error: " . $_FILES['file']['error'];
        } else {
            // Batas ukuran 10MB
            $max_size = 10 * 1024 * 1024;
            if ($_FILES['file']['size'] > $max_size) {
                $error = "Ukuran file terlalu besar. Maksimal 10MB.";
            } else {
                // Validasi ekstensi
                $allowed = ['pdf', 'ppt', 'pptx', 'doc', 'docx'];
                $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed)) {
                    $error = "Format file tidak diizinkan. Hanya PDF/PPT/DOC.";
                } else {
                    // Tentukan folder uploads
                    $upload_dir = '../assets/uploads/';
                    $target_dir = realpath(__DIR__ . '/' . $upload_dir);

                    if (!$target_dir) {
                        $error = "Folder uploads tidak ditemukan. Silakan buat folder: /assets/uploads";
                    } else {
                        // Buat folder jika belum ada
                        if (!is_dir($target_dir)) {
                            mkdir($target_dir, 0777, true);
                        }

                        // Sanitize filename
                        function sanitize_filename($filename) {
                            $filename = preg_replace('/[^a-zA-Z0-9._\-]/', '_', $filename);
                            if (strlen($filename) > 100) {
                                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                                $filename = substr($filename, 0, 100 - strlen($ext) - 1) . '.' . $ext;
                            }
                            return $filename;
                        }

                        $original_name = basename($_FILES['file']['name']);
                        $filename = uniqid() . '_' . sanitize_filename($original_name);
                        $target = $target_dir . '/' . $filename;

                        // Pindahkan file
                        if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
                            $konten = $filename;
                        } else {
                            $error = "Gagal menyimpan file. Pastikan folder uploads writable.";
                        }
                    }
                }
            }
        }
    } else {
        $konten = trim($_POST['url']);
        if (!filter_var($konten, FILTER_VALIDATE_URL)) {
            $error = "URL tidak valid.";
        }
    }

    if (empty($error)) {
        $stmt = $pdo->prepare("INSERT INTO materials (id_class, judul, tipe, konten) VALUES (?, ?, ?, ?)");
        $stmt->execute([$class_id, $judul, $tipe, $konten]);
        header("Location: manage_class.php?id=$class_id&success=1");
        exit;
    }
}
?>

<!-- Tambahkan CSS form -->
<link rel="stylesheet" href="/elearning/assets/css/form.css">

<h2>📁 Upload Materi</h2>

<?php if ($error): ?>
    <div style="background:#f8d7da; color:#721c24; padding:15px; margin:20px; border-radius:6px; text-align:center;"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if (isset($_GET['success'])): ?>
    <div class="form-success">Materi berhasil diupload.</div>
<?php endif; ?>

<div class="form-container">
    <form method="post" enctype="multipart/form-data" id="material-form">
        <input type="hidden" name="class_id" value="<?= $class_id ?>">

        <div class="form-group">
            <label class="form-label">Judul Materi:</label>
            <input type="text" name="judul" class="form-input" placeholder="Contoh: Pertemuan 1 - Pengenalan HTML" required>
        </div>

        <div class="form-group">
            <label class="form-label">Tipe Materi:</label>
            <select name="tipe" class="form-select" onchange="toggleInput(this.value)" required>
                <option value="file">File (PDF/PPT/DOC)</option>
                <option value="link">Link Eksternal</option>
                <option value="video">Video (YouTube/Vimeo)</option>
            </select>
        </div>

        <div id="file-input" class="form-group">
            <label class="form-label">Pilih File:</label>
            <input type="file" name="file" accept=".pdf,.ppt,.pptx,.doc,.docx" class="form-input">
        </div>

        <div id="url-input" class="form-group" style="display:none;">
            <label class="form-label">URL:</label>
            <input type="url" name="url" class="form-input" placeholder="https://example.com">
        </div>

        <div class="form-btns">
            <button type="submit" class="btn btn-primary">Simpan Materi</button>
            <a href="manage_class.php?id=<?= $class_id ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<script>
function toggleInput(tipe) {
    const fileInput = document.getElementById('file-input');
    const urlInput = document.getElementById('url-input');
    
    if (tipe === 'file') {
        fileInput.style.display = 'block';
        urlInput.style.display = 'none';
        fileInput.querySelector('input').setAttribute('required', 'required');
        urlInput.querySelector('input').removeAttribute('required');
    } else {
        fileInput.style.display = 'none';
        urlInput.style.display = 'block';
        fileInput.querySelector('input').removeAttribute('required');
        urlInput.querySelector('input').setAttribute('required', 'required');
    }
}

// Set default
document.addEventListener('DOMContentLoaded', function() {
    toggleInput('file');
});
</script>

<?php include '../includes/footer.php'; ?>