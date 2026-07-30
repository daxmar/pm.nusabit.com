-- ============================================================
-- DATABASE: nusabitc_pemrograman
-- Proyek: Nusabit Code Kitchen (app.nusabit.com)
-- ============================================================

CREATE DATABASE IF NOT EXISTS nusabitc_pemrograman;
USE nusabitc_pemrograman;

-- ============================================================
-- 1. Tabel users
-- Menyimpan data member & admin
-- Dipakai oleh: register.php, login.php, create_admin.php, admin/index.php
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(255) NOT NULL,
    nama_panggilan VARCHAR(100) NOT NULL,
    jenjang ENUM('sd', 'smp', 'sma', 'lainnya') DEFAULT 'smp',
    email VARCHAR(255) NOT NULL UNIQUE,
    nomer_hp VARCHAR(20) DEFAULT '-',
    alamat TEXT,
    hobi VARCHAR(255) DEFAULT '-',
    role ENUM('member', 'admin') DEFAULT 'member',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- 2. Tabel absensi
-- Menyimpan data absensi siswa (manual via form & otomatis saat login)
-- Dipakai oleh: .absen.php, login.php, .historyabsen.php
-- ============================================================
CREATE TABLE IF NOT EXISTS absensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    jenjang VARCHAR(50) DEFAULT NULL,
    nomer_hp VARCHAR(20) DEFAULT NULL,
    alasan TEXT,
    timestamp DATETIME NOT NULL,
    INDEX idx_timestamp (timestamp),
    INDEX idx_nama (nama)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- 3. Tabel quiz_answers
-- Menyimpan jawaban tugas dari member
-- Dipakai oleh: submit_tugas.php
-- ============================================================
CREATE TABLE IF NOT EXISTS quiz_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_panggilan VARCHAR(100) NOT NULL,
    materi_soal VARCHAR(255) NOT NULL,
    jawaban_1 TEXT,
    jawaban_2 TEXT,
    jawaban_3 TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_materi (materi_soal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

