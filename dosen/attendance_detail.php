<?php
require_once '../includes/header.php';
require_role(['dosen']);
require_once '../includes/functions.php';

$meeting_id = $_GET['meeting_id'] ?? 0;

// Cek apakah dosen mengampu pertemuan ini
$stmt = $pdo->prepare("
    SELECT m.*, c.id AS class_id, co.nama_mk
    FROM meetings m
    JOIN classes c ON m.id_class = c.id
    JOIN courses co ON c.id_course = co.id
    WHERE m.id = ? AND c.id_dosen = ?
");
$stmt->execute([$meeting_id, $_SESSION['user_id']]);
$meeting = $stmt->fetch();

if (!$meeting) {
    die("<h2>❌ Anda tidak berhak mengakses absensi ini.</h2>");
}

// Ambil daftar mahasiswa yang terdaftar di kelas ini
$stmt = $pdo->prepare("
    SELECT e.id_mahasiswa, u.nama_lengkap, u.email
    FROM enrollments e
    JOIN users u ON e.id_mahasiswa = u.id
    WHERE e.id_class = ?
");
$stmt->execute([$meeting['class_id']]);
$students = $stmt->fetchAll();

// Ambil data absensi
$absen = $pdo->prepare("SELECT id_mahasiswa, status, waktu_presensi FROM attendance WHERE id_meeting = ?");
$absen->execute([$meeting_id]);
$absen_data = [];
while ($row = $absen->fetch()) {
    $absen_data[$row['id_mahasiswa']] = $row;
}
?>

<h2>📋 Detail Absensi</h2>
<p><strong>Kelas:</strong> <?= htmlspecialchars($meeting['nama_mk']) ?></p>
<p><strong>Pertemuan ke:</strong> <?= $meeting['pertemuan_ke'] ?></p>
<p><strong>Tanggal:</strong> <?= $meeting['tanggal'] ?></p>
<p><strong>Topik:</strong> <?= htmlspecialchars($meeting['topik']) ?></p>

<table class="table">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Mahasiswa</th>
            <th>Status</th>
            <th>Waktu Presensi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($students as $s): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($s['nama_lengkap']) ?></td>
            <td>
                <?php
                $status = $absen_data[$s['id_mahasiswa']]['status'] ?? 'alfa';
                $color = [
                    'hadir' => 'green',
                    'sakit' => 'orange',
                    'izin' => 'blue',
                    'alfa' => 'red'
                ];
                echo "<span style='color:{$color[$status]};'>".ucfirst($status)."</span>";
                ?>
            </td>
            <td>
                <?= $absen_data[$s['id_mahasiswa']]['waktu_presensi'] ?? '-' ?>
            </td>
            <td>
                <a href="#" onclick="editStatus(<?= $s['id_mahasiswa'] ?>, '<?= $status ?>')">✏️ Edit</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="manage_class.php?id=<?= $meeting['class_id'] ?>">← Kembali ke Kelas</a>

<!-- Modal Edit Status -->
<div id="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;">
    <div style="background:white; padding:20px; margin:10% auto; width:300px; border-radius:8px;">
        <h3>Edit Status Absensi</h3>
        <form id="edit-form">
            <input type="hidden" name="student_id" id="student_id">
            <input type="hidden" name="meeting_id" value="<?= $meeting_id ?>">
            Status:
            <select name="status" id="status_select">
                <option value="hadir">Hadir</option>
                <option value="sakit">Sakit</option>
                <option value="izin">Izin</option>
                <option value="alfa">Alfa</option>
            </select><br><br>
            <button type="submit">Simpan</button>
            <button type="button" onclick="document.getElementById('modal').style.display='none'">Batal</button>
        </form>
    </div>
</div>

<script>
function editStatus(student_id, current_status) {
    document.getElementById('student_id').value = student_id;
    document.getElementById('status_select').value = current_status;
    document.getElementById('modal').style.display = 'block';
}

document.getElementById('edit-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('save_attendance.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        alert(data);
        location.reload();
    });
});
</script>

<?php include '../includes/footer.php'; ?>