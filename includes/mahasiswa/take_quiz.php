<?php
require_once '../includes/header.php';
require_role(['mahasiswa']);
require_once '../includes/functions.php';

$quiz_id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT q.*, c.id AS class_id
    FROM quizzes q
    JOIN classes c ON q.id_class = c.id
    WHERE q.id = ? AND c.id IN (
        SELECT id_class FROM enrollments WHERE id_mahasiswa = ?
    )
");
$stmt->execute([$quiz_id, $_SESSION['user_id']]);
$quiz = $stmt->fetch();

if (!$quiz) {
    die("<div style='text-align:center; padding:40px;'><h2>❌ Kuis tidak ditemukan</h2><p>Silakan kembali ke kelas.</p><a href='view_class.php?id=" . htmlspecialchars($quiz['class_id'] ?? '') . "' style='color:#3498db;'>← Kembali ke Kelas</a></div>");
}

$questions = $pdo->prepare("SELECT * FROM questions WHERE id_quiz = ?");
$questions->execute([$quiz_id]);
$questions = $questions->fetchAll();

if (empty($questions)) {
    die("<div style='text-align:center; padding:40px;'><h2>⚠️ Belum ada pertanyaan</h2><p>Kuis ini belum memiliki soal.</p><a href='view_class.php?id=" . $quiz['class_id'] . "' style='color:#3498db;'>← Kembali ke Kelas</a></div>");
}

$stmt = $pdo->prepare("SELECT 1 FROM quiz_attempts WHERE id_quiz = ? AND id_mahasiswa = ?");
$stmt->execute([$quiz_id, $_SESSION['user_id']]);
$already_done = $stmt->fetch();

if ($already_done) {
    die("<div style='text-align:center; padding:40px;'><h2>✅ Anda sudah mengerjakan kuis ini</h2><p>Hasil akan ditampilkan di halaman Nilai.</p><a href='grades.php' style='color:#3498db;'>→ Lihat Nilai</a></div>");
}

if ($_POST && isset($_POST['jawaban'])) {
    $pdo->beginTransaction();
    try {
        foreach ($_POST['jawaban'] as $question_id => $jawaban) {
            if (!in_array($jawaban, ['A','B','C','D'])) continue;

            $stmt = $pdo->prepare("SELECT jawaban_benar FROM questions WHERE id = ?");
            $stmt->execute([$question_id]);
            $kunci_benar = $stmt->fetchColumn();

            $benar = ($jawaban === $kunci_benar) ? 1 : 0;

            $stmt = $pdo->prepare("INSERT INTO quiz_attempts (id_quiz, id_mahasiswa, id_question, jawaban, benar) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$quiz_id, $_SESSION['user_id'], $question_id, $jawaban, $benar]);
        }
        $pdo->commit();

        header("Location: grades.php?success=1");
        exit;
    } catch (Exception $e) {
        $pdo->rollback();
        die("Error: " . htmlspecialchars($e->getMessage()));
    }
}
?>

<link rel="stylesheet" href="/elearning/assets/css/form.css">

<h2>🧠 <?= htmlspecialchars($quiz['judul']) ?></h2>
<p><strong>Durasi:</strong> <?= $quiz['durasi'] ?> menit</p>

<form method="post" id="quiz-form">
    <?php foreach ($questions as $q): ?>
    <div class="form-container" style="margin-bottom:30px;">
        <div class="form-group">
            <label class="form-label"><strong><?= htmlspecialchars($q['teks_pertanyaan']) ?></strong></label>
            <div style="margin-top:15px; display:flex; flex-direction:column; gap:10px;">
                <?php
                $opsi = [
                    'A' => $q['opsi_a'] ?? '',
                    'B' => $q['opsi_b'] ?? '',
                    'C' => $q['opsi_c'] ?? '',
                    'D' => $q['opsi_d'] ?? ''
                ];

                foreach ($opsi as $label => $value) {
                    $value = trim($value);
                    if (empty($value)) continue;

                    echo "
                        <label style='display:flex; align-items:center; gap:10px;'>
                            <input type='radio' name='jawaban[{$q['id']}]' value='$label' required>
                            <span>$label. " . htmlspecialchars($value) . "</span>
                        </label>
                    ";
                }
                ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <div class="form-btns">
        <button type="submit" class="btn btn-primary">Selesai & Kirim</button>
        <a href="javascript:history.back()" class="btn btn-secondary">Batal</a>
    </div>
</form>

<script>
let durasi = <?= $quiz['durasi'] ?> * 60;
const timerDiv = document.createElement('div');
timerDiv.innerHTML = `<div style="text-align:center; font-size:18px; margin:20px 0;">
    <strong>⏳ Waktu tersisa: </strong><span id="timer">-</span>
</div>`;
document.querySelector('form').before(timerDiv);

function updateTimer() {
    const minutes = Math.floor(durasi / 60);
    const seconds = durasi % 60;
    document.getElementById('timer').textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    
    if (durasi <= 0) {
        alert("Waktu habis! Jawaban Anda akan dikirim otomatis.");
        document.getElementById('quiz-form').submit();
    } else {
        durasi--;
        setTimeout(updateTimer, 1000);
    }
}
updateTimer();
</script>

<?php include '../includes/footer.php'; ?>