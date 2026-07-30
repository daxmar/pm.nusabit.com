<?php
session_start();
require_once '.config/config.php';
require_once '.config/password.php';

$error = '';
$success = '';

// CAPTCHA Logic
if (!isset($_SESSION['captcha_sum'])) {
    $num1 = rand(20, 60);
    $num2 = rand(20, 60);
    $_SESSION['captcha_sum'] = $num1 + $num2;
    $_SESSION['captcha_question'] = "$num1 + $num2";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve all form data
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $nama_panggilan = trim($_POST['nama_panggilan']);
    $jenjang = $_POST['jenjang'];
    $email = trim($_POST['email']);
    $nomer_hp = trim($_POST['nomer_hp']);
    $alamat = trim($_POST['alamat']);
    $hobi = trim($_POST['hobi']);
    $captcha_answer = trim($_POST['captcha']);

    // Basic validation
    if (empty($username) || empty($password) || empty($nama_lengkap) || empty($nama_panggilan) || empty($email) || empty($captcha_answer)) {
        $error = "Please fill all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ((int)$captcha_answer !== $_SESSION['captcha_sum']) {
        $error = "CAPTCHA answer is incorrect. Please try again.";
        // Regenerate CAPTCHA on failure
        unset($_SESSION['captcha_sum']);
    } else {
        // Check if username or email already exists
        $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "Username or Email already taken. Please choose another one.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user with all details
            $sql = "INSERT INTO users (username, password, nama_lengkap, nama_panggilan, jenjang, email, nomer_hp, alamat, hobi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            // All fields are strings except level, which is defaulted in DB
            mysqli_stmt_bind_param($stmt, "sssssssss", $username, $hashed_password, $nama_lengkap, $nama_panggilan, $jenjang, $email, $nomer_hp, $alamat, $hobi);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Registration successful! You can now log in.";
                unset($_SESSION['captcha_sum']); // Clear captcha on success
                header("refresh:2;url=login.php");
            } else {
                $error = "Something went wrong. Please try again later.";
            }
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
    <title>Register - Nusabit Code Kitchen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 py-12 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-2xl p-8 space-y-6 bg-white rounded-2xl shadow-lg">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900">
                Create Account
            </h1>
            <p class="text-gray-500 mt-2">Join the Code Kitchen</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" role="alert">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg" role="alert">
                <p><?php echo $success; ?></p>
            </div>
        <?php endif; ?>

        <form class="space-y-4" action="register.php" method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nama_lengkap" class="text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input id="nama_lengkap" name="nama_lengkap" type="text" required class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg shadow-sm">
                </div>
                <div>
                    <label for="nama_panggilan" class="text-sm font-medium text-gray-700">Nama Panggilan</label>
                    <input id="nama_panggilan" name="nama_panggilan" type="text" required class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg shadow-sm">
                </div>
            </div>
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="username" class="text-sm font-medium text-gray-700">Username <span class="text-xs text-gray-500">(nama panggilan + angka unik, contoh: dagmar123)</span></label>
                    <input id="username" name="username" type="text" required value="" class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg shadow-sm text-gray-500 italic">
                </div>
                <div>
                    <label for="password" class="text-sm font-medium text-gray-700">Password <span class="text-xs text-gray-500">(tanggal lahir, contoh: 01012010)</span></label>
                    <input id="password" name="password" type="password" required value="" class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg shadow-sm text-gray-500 italic">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="text-sm font-medium text-gray-700">Alamat Email</label>
                    <input id="email" name="email" type="email" required value="Insancendekia@gmail.com" class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg shadow-sm text-gray-500 italic">
                </div>
                <div>
                    <label for="jenjang" class="text-sm font-medium text-gray-700">Jenjang</label>
                    <select id="jenjang" name="jenjang" class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg shadow-sm">
                        <option value="sd">SD</option>
                        <option value="smp" selected>SMP</option>
                        <option value="sma">SMA</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
            </div>


            <div>
                <label for="nomer_hp" class="text-sm font-medium text-gray-700">Nomer HP (Opsional)</label>
                <input id="nomer_hp" name="nomer_hp" type="text" value="-" class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg shadow-sm text-gray-500 italic">
            </div>

            <div>
                <label for="alamat" class="text-sm font-medium text-gray-700">Alamat (Opsional)</label>
                <textarea id="alamat" name="alamat" rows="3" class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg shadow-sm" placeholder="-">-</textarea>
            </div>

            <div>
                <label for="hobi" class="text-sm font-medium text-gray-700">Hobi (Opsional, pisahkan dengan koma)</label>
                <input id="hobi" name="hobi" type="text" value="-" class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg shadow-sm text-gray-500 italic">
            </div>


            <div class="pt-2">
                <label for="captcha" class="text-sm font-medium text-gray-700">Berapakah <?php echo $_SESSION['captcha_question']; ?>?</label>
                <input id="captcha" name="captcha" type="text" required class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg shadow-sm">
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Register
                </button>
            </div>
        </form>
        <div class="text-center text-sm text-gray-500">
            Already have an account? <a href="login.php" class="font-medium text-indigo-600 hover:text-indigo-500">Login here</a>
        </div>

    <script>
    document.getElementById('username').addEventListener('input', function() {
        const username = this.value.trim();
        if (username) {
            const timestamp = Date.now().toString().slice(-6);
            document.getElementById('email').value = username + '+' + timestamp + '@gmail.com';
        }
    });
    </script>
</body>

</html>
