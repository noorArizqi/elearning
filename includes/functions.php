<?php
/**
 * Fungsi helper untuk sistem eLearning
 */

// Cek apakah dosen mengampu kelas tertentu
if (!function_exists('is_dosen_of_class')) {
    function is_dosen_of_class($pdo, $user_id, $class_id) {
        $stmt = $pdo->prepare("SELECT 1 FROM classes WHERE id = ? AND id_dosen = ?");
        $stmt->execute([$class_id, $user_id]);
        return (bool) $stmt->fetch();
    }
}

// Cek apakah mahasiswa terdaftar di kelas tertentu
if (!function_exists('is_mahasiswa_in_class')) {
    function is_mahasiswa_in_class($pdo, $user_id, $class_id) {
        $stmt = $pdo->prepare("SELECT 1 FROM enrollments WHERE id_class = ? AND id_mahasiswa = ?");
        $stmt->execute([$class_id, $user_id]);
        return (bool) $stmt->fetch();
    }
}

// Hitung jumlah balasan forum
if (!function_exists('get_reply_count')) {
    function get_reply_count($pdo, $thread_id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM forum_replies WHERE id_thread = ?");
        $stmt->execute([$thread_id]);
        return (int) $stmt->fetchColumn();
    }
}

// Ambil info pengguna lengkap
if (!function_exists('get_user_info')) {
    function get_user_info($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT u.*, p.nim_nidn, p.foto FROM users u LEFT JOIN profiles p ON u.id = p.id_user WHERE u.id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Cek apakah tugas sudah dikumpulkan
if (!function_exists('has_submitted')) {
    function has_submitted($pdo, $assignment_id, $student_id) {
        $stmt = $pdo->prepare("SELECT 1 FROM submissions WHERE id_assignment = ? AND id_mahasiswa = ?");
        $stmt->execute([$assignment_id, $student_id]);
        return (bool) $stmt->fetch();
    }
}

// Format tanggal ke bahasa Indonesia
if (!function_exists('format_date_id')) {
    function format_date_id($date) {
        if (!$date || $date == '0000-00-00 00:00:00') return '-';
        $d = new DateTime($date);
        return $d->format('d M Y H:i');
    }
}

// Ambil nama mata kuliah dari ID kelas
if (!function_exists('get_course_name')) {
    function get_course_name($pdo, $class_id) {
        $stmt = $pdo->prepare("SELECT co.nama_mk FROM classes c JOIN courses co ON c.id_course = co.id WHERE c.id = ?");
        $stmt->execute([$class_id]);
        return $stmt->fetchColumn() ?: 'Kelas Tidak Diketahui';
    }
}

// Ambil nama dosen dari ID kelas
if (!function_exists('get_dosen_name')) {
    function get_dosen_name($pdo, $class_id) {
        $stmt = $pdo->prepare("SELECT u.nama_lengkap FROM classes c JOIN users u ON c.id_dosen = u.id WHERE c.id = ?");
        $stmt->execute([$class_id]);
        return $stmt->fetchColumn() ?: 'Dosen Tidak Diketahui';
    }
}

// Ambil jumlah mahasiswa di kelas
if (!function_exists('get_student_count')) {
    function get_student_count($pdo, $class_id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE id_class = ?");
        $stmt->execute([$class_id]);
        return (int) $stmt->fetchColumn();
    }
}

// Cek status absensi mahasiswa
if (!function_exists('get_attendance_status')) {
    function get_attendance_status($pdo, $meeting_id, $student_id) {
        $stmt = $pdo->prepare("SELECT status FROM attendance WHERE id_meeting = ? AND id_mahasiswa = ?");
        $stmt->execute([$meeting_id, $student_id]);
        return $stmt->fetchColumn() ?: 'alfa';
    }
}
?>