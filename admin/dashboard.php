<?php
require_once '../includes/header.php';
require_role(['admin']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem eLearning</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: url('/elearning/assets/image.png') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            
        }

        /* Header Biru Solid */
        .header {
            background: #0145ffff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .nav-menu {
            display: flex;
            gap: 20px;
            font-size: 14px;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .nav-menu a:hover {
            background: rgba(255,255,255,0.1);
        }

        .nav-menu a i {
            margin-right: 5px;
        }

        /* Main Content */
        .container {
            max-width: 1200px;
            margin: 90px auto;
            padding: 20px;
            color: #ffffffff;
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            color: #ffffffff;
            font-size: 22px;
            font-weight: 600;
        }

        .page-title i {
            color: #ffffffff;
        }

        /* Card Grid */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px hsla(0, 0%, 0%, 0.05);
            transition: transform 0.2s, box-shadow 0.5s;
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .card h3 {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 18px;
            font-weight: 600;
        }

        .card p {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.5;
            font-size: 14px;
        }

        .card a {
            color: #0056b3;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            margin-top: auto;
            transition: color 0.2s;
        }

        .card a:hover {
            color: #004494;
            text-decoration: underline;
        }

        /* Responsif */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }
            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
            }
            .container {
                margin: 20px 15px;
                padding: 15px;
            }
            .card-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Main Content -->
    <div class="container">
        <div class="page-title">
            <i class="fas fa-chart-line"></i>
            <span>Dashboard Admin</span>
        </div>

        <div class="card-grid">
            <!-- Card 1: Manajemen Pengguna -->
            <div class="card">
                <h3><i class="fas fa-users"></i> Manajemen Pengguna</h3>
                <p>Kelola akun Admin, Dosen, dan Mahasiswa.</p>
                <a href="manage_users.php">Lihat Semua Pengguna</a>
            </div>

            <!-- Card 2: Mata Kuliah -->
            <div class="card">
                <h3><i class="fas fa-book"></i> Mata Kuliah</h3>
                <p>Buat dan kelola daftar mata kuliah.</p>
                <a href="manage_courses.php">Kelola MK</a>
            </div>

            <!-- Card 3: Kelas -->
            <div class="card">
                <h3><i class="fas fa-school"></i> Kelas</h3>
                <p>Buat kelas baru dan hubungkan dengan dosen & mata kuliah.</p>
                <a href="create_class.php">Buat Kelas</a>
            </div>

            <!-- Card 4: Pendaftaran Mahasiswa -->
            <div class="card">
                <h3><i class="fas fa-graduation-cap"></i> Pendaftaran Mahasiswa</h3>
                <p>Daftarkan mahasiswa ke kelas yang tersedia.</p>
                <a href="enroll_students.php">Daftarkan Mahasiswa</a>
            </div>

            <!-- Card 5: Sistem -->
            <div class="card">
                <h3><i class="fas fa-cog"></i> Sistem</h3>
                <p>Pengaturan global aplikasi.</p>
                <a href="system_settings.php">Pengaturan</a>
            </div>
        </div>
    </div>

</body>
</html>

<?php include '../includes/footer.php'; ?>