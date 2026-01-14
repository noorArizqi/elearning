<?php
session_start();
require_once '../config/database.php'; // Asumsikan kita punya file koneksi database

// Cek apakah admin sudah login, jika belum redirect ke halaman login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../public/login.php");
    exit;
}

// Ambil daftar mahasiswa dan mata kuliah
$students = $pdo->query("SELECT * FROM students")->fetchAll(PDO::FETCH_ASSOC);
$courses = $pdo->query("SELECT * FROM courses")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $grade = $_POST['grade'];
    $semester = $_POST['semester'];
    $academic_year = $_POST['academic_year'];

    // Validasi
    if (empty($student_id) || empty($course_id) || empty($grade) || empty($semester) || empty($academic_year)) {
        $error = "Semua field harus diisi!";
    } else {
        // Cek apakah nilai untuk mahasiswa di mata kuliah tersebut pada semester dan tahun akademik tersebut sudah ada
        $stmt = $pdo->prepare("SELECT * FROM khs WHERE student_id = ? AND course_id = ? AND semester = ? AND academic_year = ?");
        $stmt->execute([$student_id, $course_id, $semester, $academic_year]);
        if ($stmt->rowCount() > 0) {
            $error = "Nilai untuk mahasiswa ini pada mata kuliah, semester, dan tahun akademik ini sudah ada!";
        } else {
            // Insert nilai
            $stmt = $pdo->prepare("INSERT INTO khs (student_id, course_id, grade, semester, academic_year) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$student_id, $course_id, $grade, $semester, $academic_year])) {
                $success = "Nilai berhasil ditambahkan!";
            } else {
                $error = "Gagal menambahkan nilai!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Nilai</title>
    <link rel="stylesheet" href="../assets/css/forms.css">
</head>
<body>
    <div class="container">
        <h1>Input Nilai Mahasiswa</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="student_id">Mahasiswa:</label>
                <select name="student_id" id="student_id" required>
                    <option value="">Pilih Mahasiswa</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?php echo $student['id']; ?>"><?php echo $student['nim'] . ' - ' . $student['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="course_id">Mata Kuliah:</label>
                <select name="course_id" id="course_id" required>
                    <option value="">Pilih Mata Kuliah</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo $course['id']; ?>"><?php echo $course['kode'] . ' - ' . $course['nama']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="grade">Nilai:</label>
                <select name="grade" id="grade" required>
                    <option value="">Pilih Nilai</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                </select>
            </div>
            <div class="form-group">
                <label for="semester">Semester:</label>
                <input type="text" name="semester" id="semester" required placeholder="Contoh: Ganjil 2023/2024">
            </div>
            <div class="form-group">
                <label for="academic_year">Tahun Akademik:</label>
                <input type="text" name="academic_year" id="academic_year" required placeholder="Contoh: 2023/2024">
            </div>
            <div class="form-group">
                <button type="submit">Simpan</button>
            </div>
        </form>
    </div>
</body>
</html>

