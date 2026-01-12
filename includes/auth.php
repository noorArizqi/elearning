<?php
if (!session_id()) {
    session_start();
}
require_once 'config.php';

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: index.php');
        exit;
    }
}

function require_role($allowed_roles) {
    require_login();
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        http_response_code(403);
        die("<h2>⛔ Akses Ditolak</h2><p>Anda tidak memiliki izin mengakses halaman ini.</p>");
    }
}

// ❌ HAPUS fungsi get_user_info() dari sini
// Karena sudah ada di functions.php
?>

<?php
if (!session_id()) {
    session_start();
}
require_once 'config.php';

// ... fungsi auth ...

// Muat fungsi helper
require_once __DIR__ . '/functions.php';
?>