<?php
require_once '../includes/header.php';
require_role(['dosen']);

$class_id = $_GET['class_id'] ?? 0;
if (!is_dosen_of_class($pdo, $_SESSION['user_id'], $class_id)) {
    die("Akses ditolak.");
}

if ($_POST) {
    $judul = trim($_POST['judul']);
    $durasi = (int)$_POST['durasi'];
    
    if (empty($judul) || $durasi <= 0) {
        die("Judul dan durasi wajib diisi dengan benar.");
    }

    try {
        $pdo->beginTransaction();
        
        // Simpan kuis
        $stmt = $pdo->prepare("INSERT INTO quizzes (id_class, judul, durasi) VALUES (?, ?, ?)");
        $stmt->execute([$class_id, $judul, $durasi]);
        $quiz_id = $pdo->lastInsertId();

        // Ambil data pertanyaan
        $teks_pertanyaan = $_POST['teks_pertanyaan'] ?? '';
        $opsi_a = trim($_POST['opsi_a'] ?? '');
        $opsi_b = trim($_POST['opsi_b'] ?? '');
        $opsi_c = trim($_POST['opsi_c'] ?? '');
        $opsi_d = trim($_POST['opsi_d'] ?? '');
        $jawaban_benar = strtoupper(trim($_POST['jawaban_benar'] ?? 'A'));

        // Validasi
        if (empty($teks_pertanyaan)) {
            throw new Exception("Teks pertanyaan wajib diisi.");
        }
        if (empty($opsi_a) && empty($opsi_b) && empty($opsi_c) && empty($opsi_d)) {
            throw new Exception("Setidaknya satu opsi harus diisi.");
        }
        if (!in_array($jawaban_benar, ['A','B','C','D'])) {
            throw new Exception("Jawaban benar harus A, B, C, atau D.");
        }

        // Simpan pertanyaan
        $stmt = $pdo->prepare("INSERT INTO questions (id_quiz, teks_pertanyaan, tipe, opsi_a, opsi_b, opsi_c, opsi_d, jawaban_benar) VALUES (?, ?, 'PG', ?, ?, ?, ?, ?)");
        $stmt->execute([$quiz_id, $teks_pertanyaan, $opsi_a, $opsi_b, $opsi_c, $opsi_d, $jawaban_benar]);

        $pdo->commit();
        header("Location: manage_class.php?id=$class_id&success=1");
        exit;
    } catch (Exception $e) {
        $pdo->rollback();
        die("Error: " . htmlspecialchars($e->getMessage()));
    }
}
?>

<link rel="stylesheet" href="/elearning/assets/css/form.css">

<h2>🧠 Buat Kuis Pilihan Ganda</h2>

<?php if (isset($_GET['success'])): ?>
    <div class="form-success">Kuis berhasil dibuat.</div>
<?php endif; ?>

<div class="form-container">
    <form method="post">
        <div class="form-group">
            <label class="form-label">Judul Kuis:</label>
            <input type="text" name="judul" class="form-input" placeholder="Contoh: Kuis HTML Dasar" required>
        </div>

        <div class="form-group">
            <label class="form-label">Durasi (menit):</label>
            <input type="number" name="durasi" min="1" max="120" value="15" class="form-input" required>
        </div>

        <div class="form-group">
            <label class="form-label">Teks Pertanyaan:</label>
            <textarea name="teks_pertanyaan" class="form-input" placeholder="Masukkan pertanyaan..." rows="3" required></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Opsi A:</label>
            <input type="text" name="opsi_a" class="form-input" placeholder="Opsi A" required>
        </div>

        <div class="form-group">
            <label class="form-label">Opsi B:</label>
            <input type="text" name="opsi_b" class="form-input" placeholder="Opsi B">
        </div>

        <div class="form-group">
            <label class="form-label">Opsi C:</label>
            <input type="text" name="opsi_c" class="form-input" placeholder="Opsi C">
        </div>

        <div class="form-group">
            <label class="form-label">Opsi D:</label>
            <input type="text" name="opsi_d" class="form-input" placeholder="Opsi D">
        </div>

        <div class="form-group">
            <label class="form-label">Jawaban Benar:</label>
            <select name="jawaban_benar" class="form-select" required>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>
        </div>

        <div class="form-btns">
            <button type="submit" class="btn btn-primary">Simpan Kuis</button>
            <a href="manage_class.php?id=<?= $class_id ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>