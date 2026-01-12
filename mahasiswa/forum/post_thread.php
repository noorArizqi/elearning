<?php
session_start();
require_once '../../includes/config.php';

if ($_POST) {
    $class_id = $_POST['class_id'];
    $judul = trim($_POST['judul']);
    $isi = trim($_POST['isi']);
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO forum_threads (id_class, id_user, judul, isi) VALUES (?, ?, ?, ?)");
    $stmt->execute([$class_id, $user_id, $judul, $isi]);

    header("Location: view_forum.php?id=$class_id");
}
?>