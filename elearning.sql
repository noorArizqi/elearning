-- Buat database
CREATE DATABASE IF NOT EXISTS elearning CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE elearning;

-- Tabel users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'dosen', 'mahasiswa') NOT NULL
) ENGINE=InnoDB;

-- Tabel profiles
CREATE TABLE profiles (
    id_user INT PRIMARY KEY,
    nim_nidn VARCHAR(20),
    foto VARCHAR(255),
    alamat TEXT,
    fakultas VARCHAR(100),
    prodi VARCHAR(100),
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabel courses
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_mk VARCHAR(20) UNIQUE NOT NULL,
    nama_mk VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    sks TINYINT NOT NULL CHECK (sks BETWEEN 1 AND 6)
) ENGINE=InnoDB;

-- Tabel classes
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_course INT NOT NULL,
    id_dosen INT NOT NULL,
    tahun_akademik YEAR NOT NULL,
    semester ENUM('Ganjil', 'Genap') NOT NULL,
    FOREIGN KEY (id_course) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (id_dosen) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Tabel enrollments
CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_class INT NOT NULL,
    id_mahasiswa INT NOT NULL,
    FOREIGN KEY (id_class) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (id_mahasiswa) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE(id_class, id_mahasiswa)
) ENGINE=InnoDB;

-- Tabel materials
CREATE TABLE materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_class INT NOT NULL,
    judul VARCHAR(150) NOT NULL,
    tipe ENUM('file', 'link', 'video') NOT NULL,
    konten TEXT NOT NULL,
    urutan_tampil INT DEFAULT 0,
    FOREIGN KEY (id_class) REFERENCES classes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabel assignments
CREATE TABLE assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_class INT NOT NULL,
    judul VARCHAR(150) NOT NULL,
    deskripsi TEXT,
    deadline DATETIME NOT NULL,
    skor_maksimal INT NOT NULL CHECK (skor_maksimal > 0)
) ENGINE=InnoDB;

-- Tabel submissions
CREATE TABLE submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_assignment INT NOT NULL,
    id_mahasiswa INT NOT NULL,
    file_tugas VARCHAR(255),
    waktu_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    nilai DECIMAL(5,2) DEFAULT NULL,
    feedback_dosen TEXT,
    FOREIGN KEY (id_assignment) REFERENCES assignments(id) ON DELETE CASCADE,
    FOREIGN KEY (id_mahasiswa) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE(id_assignment, id_mahasiswa)
) ENGINE=InnoDB;

-- Tabel quizzes
CREATE TABLE quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_class INT NOT NULL,
    judul VARCHAR(150) NOT NULL,
    durasi INT NOT NULL -- dalam menit
) ENGINE=InnoDB;

-- Tabel questions
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_quiz INT NOT NULL,
    teks_pertanyaan TEXT NOT NULL,
    tipe ENUM('PG', 'Essay') NOT NULL,
    kunci_jawaban TEXT,
    FOREIGN KEY (id_quiz) REFERENCES quizzes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabel meetings
CREATE TABLE meetings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_class INT NOT NULL,
    pertemuan_ke TINYINT NOT NULL CHECK (pertemuan_ke BETWEEN 1 AND 16),
    tanggal DATE NOT NULL,
    topik VARCHAR(150)
) ENGINE=InnoDB;

-- Tabel attendance
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_meeting INT NOT NULL,
    id_mahasiswa INT NOT NULL,
    status ENUM('hadir', 'sakit', 'izin', 'alfa') DEFAULT 'alfa',
    waktu_presensi DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_meeting) REFERENCES meetings(id) ON DELETE CASCADE,
    FOREIGN KEY (id_mahasiswa) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE(id_meeting, id_mahasiswa)
) ENGINE=InnoDB;

-- Tabel forum_threads
CREATE TABLE forum_threads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_class INT NOT NULL,
    id_user INT NOT NULL,
    judul VARCHAR(150) NOT NULL,
    isi TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_class) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabel forum_replies
CREATE TABLE forum_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_thread INT NOT NULL,
    id_user INT NOT NULL,
    isi_komentar TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_thread) REFERENCES forum_threads(id) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ================================
-- [OPSIONAL] Data Awal untuk Testing
-- ================================

-- Admin
INSERT INTO users (username, password, nama_lengkap, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Utama', 'admin@univ.ac.id', 'admin');

-- Dosen
INSERT INTO users (username, password, nama_lengkap, email, role) VALUES
('dosen1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Budi Santoso', 'budi@univ.ac.id', 'dosen');

-- Mahasiswa
INSERT INTO users (username, password, nama_lengkap, email, role) VALUES
('mhs123', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ani Lestari', 'ani123@univ.ac.id', 'mahasiswa');

-- Profil
INSERT INTO profiles (id_user, nim_nidn, fakultas, prodi) VALUES
(1, NULL, 'Teknik', 'Sistem Informasi'),
(2, '19870101', 'Teknik', 'Teknik Informatika'),
(3, '23105123', 'Ilmu Komputer', 'Teknologi Informasi');

-- Mata Kuliah
INSERT INTO courses (kode_mk, nama_mk, deskripsi, sks) VALUES
('INF202', 'Pemrograman Web', 'Membangun aplikasi web dinamis', 3);

-- Kelas
INSERT INTO classes (id_course, id_dosen, tahun_akademik, semester) VALUES
(1, 2, 2025, 'Ganjil');

-- Enrollments
INSERT INTO enrollments (id_class, id_mahasiswa) VALUES
(1, 3);

-- Note: Password default untuk semua akun di atas adalah: "password"
-- Hash dihasilkan dari: password_hash('password', PASSWORD_DEFAULT)