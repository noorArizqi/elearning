<?php
require_once 'includes/config.php';

$error = '';

if ($_POST) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nama'] = $user['nama_lengkap'];

        switch ($user['role']) {
            case 'admin': header('Location: admin/dashboard.php'); break;
            case 'dosen': header('Location: dosen/dashboard.php'); break;
            case 'mahasiswa': header('Location: mahasiswa/dashboard.php'); break;
        }
        exit;
    } else {
        $error = "Username atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login eLearning</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            height: 100vh;
            background: #f8f9fa;
        }

        .logo-section {
            width: 50%;
            background: #0056b3;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .logo {
            max-width: 80%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .login-section {
            width: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
            background: white;
        }

        .login-form {
            width: 100%;
            max-width: 360px;
            text-align: left;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: 600;
            letter-spacing: 0.5px;
            font-size: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            color: #555;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #0056b3;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #7f8c8d;
            font-size: 16px;
        }

        .password-wrapper {
            position: relative;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: #0056b3;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: background 0.2s;
        }

        .btn:hover {
            background: #004494;
        }

        .forgot-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            font-size: 13px;
            color: #0056b3;
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .error {
            color: #e74c3c;
            margin: 10px 0;
            font-size: 13px;
            padding: 8px;
            background: #fdf2f2;
            border-radius: 4px;
            border-left: 3px solid #e74c3c;
        }

        /* Responsif */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            .logo-section, .login-section {
                width: 100%;
                padding: 30px 20px;
            }
            .logo {
                max-width: 60%;
            }
        }
    </style>
</head>
<body>

<div class="logo-section">
    <!-- Logo Kampus -->
    <img src="/elearning/assets/logo.png" 
         alt="Logo Institut Teknologi Statistika dan Bisnis Muhammadiyah Semarang" 
         class="logo"
         onerror="this.src='/elearning/assets/images/default-logo.png'; this.alt='Logo Default'">
</div>

<div class="login-section">
    <div class="login-form">
        <h2>LOG IN</h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="username">NIM</label>
                <input type="text" name="username" id="username" placeholder="Nim Mahasiswa" class="form-input" required autocomplete="username">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password" placeholder="Enter your password" class="form-input" required autocomplete="current-password">
                    <span class="toggle-password" onclick="togglePassword()">
                        <i class="far fa-eye" id="eye-icon"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn">LOG IN</button>
            <a href="#" class="forgot-link">Forgot Password</a>
        </form>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}
</script>

</body>
</html>