<?php
session_start();
require_once '../includes/config.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="nilai_saya_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Kode MK', 'Mata Kuliah', 'Tugas', 'Nilai', 'Skor Maks', 'Persentase']);

$stmt = $pdo->prepare("
    SELECT 
        co.kode_mk,
        co.nama_mk,
        a.judul AS tugas,
        s.nilai,
        a.skor_maksimal
    FROM submissions s
    JOIN assignments a ON s.id_assignment = a.id
    JOIN classes c ON a.id_class = c.id
    JOIN courses co ON c.id_course = co.id
    WHERE s.id_mahasiswa = ? AND s.nilai IS NOT NULL
    ORDER BY co.kode_mk, a.deadline
");
$stmt->execute([$_SESSION['user_id']]);

while ($row = $stmt->fetch()) {
    $persen = $row['skor_maksimal'] > 0 ? round(($row['nilai'] / $row['skor_maksimal']) * 100, 2) : 0;
    fputcsv($output, [
        $row['kode_mk'],
        $row['nama_mk'],
        $row['tugas'],
        $row['nilai'],
        $row['skor_maksimal'],
        $persen . '%'
    ]);
}

fclose($output);
exit;
?>