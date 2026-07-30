<?php
session_start();
require_once '.config/config.php';
require_once '.config/password.php';

$error = '';

if (isset($_GET['timeout']) && $_GET['timeout'] == 'true') {
    $error = "Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan login kembali.";
}

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please enter username and password.";
    } else {
        $sql = "SELECT id, username, password, nama_panggilan, nama_lengkap, jenjang, nomer_hp, role FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $nama_panggilan, $nama_lengkap, $jenjang, $nomer_hp, $role);
                if (mysqli_stmt_fetch($stmt)) {
                    if (password_verify($password, $hashed_password)) {
                        
                        // --- Automatic Attendance Logic ---
                        // Check if already attended today
                        $today_check_sql = "SELECT id FROM absensi WHERE nama = ? AND DATE(timestamp) = CURDATE()";
                        $today_stmt = mysqli_prepare($conn, $today_check_sql);
                        mysqli_stmt_bind_param($today_stmt, "s", $nama_lengkap);
                        mysqli_stmt_execute($today_stmt);
                        mysqli_stmt_store_result($today_stmt);

                        if (mysqli_stmt_num_rows($today_stmt) == 0) {
                            // Not attended yet, so insert into absensi
                            $absen_sql = "INSERT INTO absensi (nama, jenjang, nomer_hp, alasan, timestamp) VALUES (?, ?, ?, 'Login', NOW())";
                            $absen_stmt = mysqli_prepare($conn, $absen_sql);
                            mysqli_stmt_bind_param($absen_stmt, "sss", $nama_lengkap, $jenjang, $nomer_hp);
                            mysqli_stmt_execute($absen_stmt);
                            mysqli_stmt_close($absen_stmt);
                        }
                        mysqli_stmt_close($today_stmt);
                        // --- End of Automatic Attendance Logic ---

                        // Store data in session variables
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username;
                        $_SESSION["nama_panggilan"] = $nama_panggilan;
                        $_SESSION["jenjang"] = $jenjang;
                        $_SESSION["role"] = $role;
                        
                        // Redirect user to home page
                        header("location: index.php");
                        exit(); // Important to exit after header redirect

                    } else {
                        $error = "The password you entered was not valid.";
                    }
                }
            } else {
                $error = "No account found with that username.";
            }
        } else {
            $error = "Oops! Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Nusabit Code Kitchen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-2xl shadow-lg">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900">
                Welcome Back!
            </h1>
            <p class="text-gray-500 mt-2">Sign in to continue</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" role="alert">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <form class="space-y-6" action="login.php" method="POST">
            <div>
                <label for="username" class="text-sm font-medium text-gray-700">Username</label>
                <input id="username" name="username" type="text" required
                       class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="password" class="text-sm font-medium text-gray-700">Password</label>
                <input id="password" name="password" type="password" required
                       class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Sign In
                </button>
            </div>
        </form>
        <div class="text-center text-sm text-gray-500">
            Don't have an account? <a href="register.php" class="font-medium text-indigo-600 hover:text-indigo-500">Register here</a>
        </div>
    </div>
</body>
</html>
