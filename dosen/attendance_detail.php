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
<style>
/* Reset & Base */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f8fafc;
    color: #333;
    line-height: 1.6;
}
.container {
    max-width: 900px;
    margin: 2rem auto;
    padding: 0 1rem;
}

/* Heading */
.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Info Box */
.info-box {
    background: white;
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
}
.info-box p {
    margin: 0.4rem 0;
    font-size: 1rem;
}
.info-box strong {
    display: inline-block;
    width: 100px;
    color: #4a5568;
}

/* Table */
.table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
    margin-bottom: 1.5rem;
}
table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    padding: 1rem 1.25rem;
    text-align: left;
}
th {
    background-color: #f8fafc;
    font-weight: 600;
    color: #4a5568;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
tbody tr {
    transition: background-color 0.2s;
}
tbody tr:hover {
    background-color: #f8fafc;
}
td {
    font-size: 0.95rem;
    color: #2d3748;
}

/* Status Badge */
.badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.85rem;
    font-weight: 600;
}
.badge.hadir { background-color: #dcfce7; color: #166534; }
.badge.sakit { background-color: #ffedd5; color: #9a3412; }
.badge.izin  { background-color: #dbeafe; color: #1d4ed8; }
.badge.alfa  { background-color: #fee2e2; color: #b91c1c; }

/* Action Link */
.action-link {
    color: #4f46e5;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}
.action-link:hover {
    color: #4338ca;
    text-decoration: underline;
}

/* Back Button */
.back-btn {
    display: inline-block;
    background-color: #f1f5f9;
    color: #334155;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.95rem;
    transition: background-color 0.2s;
}
.back-btn:hover {
    background-color: #e2e8f0;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}
.modal-content {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    width: 90%;
    max-width: 400px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}
.modal h3 {
    margin-bottom: 1rem;
    color: #1e293b;
    font-size: 1.25rem;
}
.form-group {
    margin-bottom: 1.25rem;
}
.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #475569;
}
.form-group select {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 1rem;
    background: white;
}
.form-group button {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s;
}
.btn-save {
    background-color: #4f46e5;
    color: white;
    flex: 1;
    margin-right: 0.5rem;
}
.btn-save:hover {
    background-color: #4338ca;
}
.btn-cancel {
    background-color: #e2e8f0;
    color: #334155;
    flex: 1;
}
.btn-cancel:hover {
    background-color: #cbd5e1;
}
.modal-buttons {
    display: flex;
    gap: 0.5rem;
}

/* Error */
.error {
    color: #e53e3e;
    text-align: center;
    margin-top: 2rem;
}
</style>

<div class="container">
    <h2 class="page-title">📋 Detail Absensi</h2>

    <div class="info-box">
        <p><strong>Kelas:</strong> <?= htmlspecialchars($meeting['nama_mk']) ?></p>
        <p><strong>Pertemuan ke:</strong> <?= $meeting['pertemuan_ke'] ?></p>
        <p><strong>Tanggal:</strong> <?= date('d M Y', strtotime($meeting['tanggal'])) ?></p>
        <p><strong>Topik:</strong> <?= htmlspecialchars($meeting['topik']) ?></p>
    </div>

    <div class="table-container">
        <table>
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
                    <?php
                    $status = $absen_data[$s['id_mahasiswa']]['status'] ?? 'alfa';
                    $waktu = $absen_data[$s['id_mahasiswa']]['waktu_presensi'] ?? '-';
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($s['nama_lengkap']) ?></td>
                        <td>
                            <span class="badge <?= $status ?>"><?= ucfirst($status) ?></span>
                        </td>
                        <td><?= $waktu ?></td>
                        <td>
                            <a href="#" class="action-link" onclick="editStatus(<?= $s['id_mahasiswa'] ?>, '<?= $status ?>')">✏️ Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <a href="manage_class.php?id=<?= $meeting['class_id'] ?>" class="back-btn">← Kembali ke Kelas</a>
</div>

<!-- Modal -->
<div id="modal" class="modal">
    <div class="modal-content">
        <h3>Edit Status Absensi</h3>
        <form id="edit-form">
            <input type="hidden" name="student_id" id="student_id">
            <input type="hidden" name="meeting_id" value="<?= $meeting_id ?>">

            <div class="form-group">
                <label for="status_select">Status</label>
                <select name="status" id="status_select">
                    <option value="hadir">Hadir</option>
                    <option value="sakit">Sakit</option>
                    <option value="izin">Izin</option>
                    <option value="alfa">Alfa</option>
                </select>
            </div>

            <div class="modal-buttons">
                <button type="submit" class="btn-save">Simpan</button>
                <button type="button" class="btn-cancel" onclick="document.getElementById('modal').style.display='none'">Batal</button>
            </div>
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


