<?php

//offline
// DB credentials
//$dbHost = 'localhost';
//$dbUser = 'root';
//$dbPass = ''; // IMPORTANT: Fill this with your MySQL root password
//$dbName = 'nusabitc_pemrograman';
date_default_timezone_set('Asia/Jakarta');

//online
$dbHost = 'localhost';
$dbUser = 'nusabitc_pemrograman';
$dbPass = '&7}wv]qGyXw%X{Hj'; // IMPORTANT: Fill this with your MySQL root password
$dbName = 'nusabitc_pemrograman2';


// Create database connection
$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}   
    // --- Konfigurasi Auto Logout (Session Timeout) ---
$timeout_duration = 1800; // 30 menit dalam detik (ubah sesuai kebutuhan)

// Cek apakah session sudah dimulai dan user sedang login
if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Cek waktu aktivitas terakhir
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
        // Jika durasi inaktivitas melebihi batas, hapus session
        session_unset();
        session_destroy();
        
        // Redirect ke halaman login dengan pesan timeout
        header("location: login.php?timeout=true");
        exit;
    }
    // Update waktu aktivitas terakhir setiap kali user memuat halaman
    $_SESSION['last_activity'] = time();
}
?>