<?php
require_once '../includes/header.php';
require_role(['admin']);

// Ambil setting sistem
$stmt = $pdo->prepare("SELECT * FROM settings WHERE id = 1");
$stmt->execute();
$setting = $stmt->fetch();

if (!$setting) {
    $stmt = $pdo->prepare("INSERT INTO settings (app_name, year_academic, email_contact) VALUES (?, ?, ?)");
    $stmt->execute(['Sistem eLearning', date('Y'), 'admin@univ.ac.id']);
    $setting = [
        'app_name' => 'Sistem eLearning',
        'year_academic' => date('Y'),
        'email_contact' => 'admin@univ.ac.id'
    ];
}

if ($_POST) {
    $app_name = trim($_POST['app_name']);
    $year_academic = $_POST['year_academic'];
    $email_contact = trim($_POST['email_contact']);

    $stmt = $pdo->prepare("UPDATE settings SET app_name = ?, year_academic = ?, email_contact = ? WHERE id = 1");
    $stmt->execute([$app_name, $year_academic, $email_contact]);

    header("Location: system_settings.php?success=1");
    exit;
}
?>

<!-- Tambahkan CSS -->
<link rel="stylesheet" href="/elearning/assets/css/form.css">

<h2>⚙️ Pengaturan Sistem</h2>

<?php if (isset($_GET['success'])): ?>
    <div class="form-success">Pengaturan berhasil disimpan.</div>
<?php endif; ?>

<div class="form-container">
    <form method="post">
        <div class="form-group">
            <label class="form-label">Nama Aplikasi:</label>
            <input type="text" name="app_name" value="<?= htmlspecialchars($setting['app_name']) ?>" class="form-input" required>
        </div>

        <div class="form-group">
            <label class="form-label">Tahun Akademik:</label>
            <input type="number" name="year_academic" value="<?= $setting['year_academic'] ?>" min="2020" max="2030" class="form-input" required>
        </div>

        <div class="form-group">
            <label class="form-label">Email Kontak:</label>
            <input type="email" name="email_contact" value="<?= htmlspecialchars($setting['email_contact']) ?>" class="form-input" required>
        </div>

        <div class="form-btns">
            <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
            <a href="dashboard.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>