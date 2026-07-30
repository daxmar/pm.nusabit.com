<?php
session_start();
require_once '.config/config.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header("location: login.php?redirect_url=" . urlencode($_SERVER['HTTP_REFERER']));
    exit;
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user details from session
    $user_id = $_SESSION['id'];
    $nama_panggilan = $_SESSION['nama_panggilan'];

    // Sanitize and retrieve form data
    $materi_soal = isset($_POST['materi_soal']) ? trim($_POST['materi_soal']) : 'unknown';
    $jawaban_1 = isset($_POST['jawaban_1']) ? trim($_POST['jawaban_1']) : '';
    $jawaban_2 = isset($_POST['jawaban_2']) ? trim($_POST['jawaban_2']) : '';
    $jawaban_3 = isset($_POST['jawaban_3']) ? trim($_POST['jawaban_3']) : '';

    // Prepare an insert statement
    $sql = "INSERT INTO quiz_answers (user_id, nama_panggilan, materi_soal, jawaban_1, jawaban_2, jawaban_3) VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "isssss", $user_id, $nama_panggilan, $materi_soal, $jawaban_1, $jawaban_2, $jawaban_3);

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Redirect to index page with a success message
            $_SESSION['tugas_success'] = "Jawaban untuk " . htmlspecialchars($materi_soal) . " berhasil dikirim!";
            header("location: index.php");
            exit;
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }
} else {
    // If the page was accessed directly without POST data, redirect to home
    header("location: index.php");
    exit;
}

// Close connection
mysqli_close($conn);
?>