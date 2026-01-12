<?php
session_start();
require_once '../../includes/config.php';

if ($_POST) {
    $thread_id = $_POST['thread_id'];
    $isi = trim($_POST['isi_komentar']);
    $user_id = $_SESSION['user_id'];

    // Validasi thread
    $stmt = $pdo->prepare("SELECT 1 FROM forum_threads WHERE id = ?");
    $stmt->execute([$thread_id]);
    if (!$stmt->fetch()) {
        die("Thread tidak ditemukan.");
    }

    $stmt = $pdo->prepare("INSERT INTO forum_replies (id_thread, id_user, isi_komentar) VALUES (?, ?, ?)");
    $stmt->execute([$thread_id, $user_id, $isi]);

    // Kirim notifikasi ke pembuat thread
    $stmt = $pdo->prepare("SELECT id_user FROM forum_threads WHERE id = ?");
    $stmt->execute([$thread_id]);
    $thread_owner = $stmt->fetchColumn();

    if ($thread_owner != $user_id) {
        $msg = "Ada balasan baru di thread: " . substr($isi, 0, 100); // batasi panjang, jangan pakai htmlspecialchars
        $link = "/elearning/mahasiswa/forum/view_replies.php?thread_id=" . $thread_id;
        $pdo->prepare("INSERT INTO notifications (id_user, id_from, message, link) VALUES (?, ?, ?, ?)")
             ->execute([$thread_owner, $user_id, $msg, $link]);
    }

    header("Location: view_replies.php?thread_id=$thread_id");
    exit;
}
?>