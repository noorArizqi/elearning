<?php
require_once '../includes/header.php';
require_role(['dosen']);

$meeting_id = $_GET['meeting_id'] ?? 0;

// Ambil info meeting
$stmt = $pdo->prepare("
    SELECT m.pertemuan_ke, c.id AS class_id, co.nama_mk
    FROM meetings m
    JOIN classes c ON m.id_class = c.id
    JOIN courses co ON c.id_course = co.id
    WHERE m.id = ?
");
$stmt->execute([$meeting_id]);
$meeting = $stmt->fetch();

if (!$meeting) {
    die("<h2>❌ Pertemuan tidak ditemukan.</h2>");
}

// URL absensi untuk mahasiswa
$absen_url = "http://" . $_SERVER['HTTP_HOST'] . "/elearning/mahasiswa/scan_absen.php?meeting_id=" . $meeting_id;

// Fungsi generate QR sederhana (tanpa library)
function generateQR($url, $size = 300) {
    return "https://chart.googleapis.com/chart?cht=qr&chs={$size}x{$size}&chl=" . urlencode($url);
}
?>

<h2>Absensi: <?= htmlspecialchars($meeting['nama_mk']) ?> - Pertemuan <?= $meeting['pertemuan_ke'] ?></h2>

<div style="text-align:center; margin: 30px 0;">
    <img src="<?= generateQR($absen_url) ?>" alt="QR Absensi" style="border:1px solid #ccc; padding:10px;">
    <p><small>Scan QR ini untuk absen</small></p>
    <p><a href="<?= $absen_url ?>">🔗 Link Absensi Langsung</a></p>
</div>

<a href="manage_class.php?id=<?= $meeting['class_id'] ?>">← Kembali ke Kelas</a>

<?php include '../includes/footer.php'; ?>