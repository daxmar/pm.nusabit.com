<?php
session_start();

require_once '.config/config.php';

$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$captchaError = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nama'])) {
    if (isset($_POST['captcha'], $_SESSION['captcha_answer']) && intval($_POST['captcha']) == $_SESSION['captcha_answer']) {
        $nama = mysqli_real_escape_string($conn, strip_tags($_POST['nama']));
        $jenjang = mysqli_real_escape_string($conn, strip_tags($_POST['jenjang']));
        $nomer_hp = mysqli_real_escape_string($conn, strip_tags($_POST['nomer_hp']));
        $alasan = mysqli_real_escape_string($conn, strip_tags($_POST['alasan']));

        if (!empty($nama)) {
            $sql = "INSERT INTO absensi (nama, jenjang, nomer_hp, alasan, timestamp) VALUES ('$nama', '$jenjang', '$nomer_hp', '$alasan', NOW())";
            mysqli_query($conn, $sql);
            unset($_SESSION['captcha_answer']);
            header("Location: index.php"); // Redirect back to index
            exit;
        }
    } else {
        $captchaError = 'Jawaban captcha salah, silakan coba lagi.';
    }
}

// Captcha logic
if (!isset($_SESSION['captcha_answer']) || $captchaError === '') {
    $num1 = rand(1, 20);
    $num2 = rand(1, 20);
    $_SESSION['num1'] = $num1;
    $_SESSION['num2'] = $num2;
    $_SESSION['captcha_answer'] = $num1 + $num2;
} else {
    $num1 = $_SESSION['num1'];
    $num2 = $_SESSION['num2'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="https://nusabit.com/assets/logo-48.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absen - Nusabit Code Kitchen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Space+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center">

    <div class="max-w-2xl w-full mx-auto p-6">
        <div class="text-center mb-8">
             <a href="index.php" class="text-gray-500 hover:text-indigo-600 mb-8 inline-block"><i class="fa-solid fa-arrow-left"></i> Kembali ke Menu</a>
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4 tracking-tight">
                Absensi <span class="text-indigo-600 italic">Dulu.</span>
            </h1>
        </div>
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <form action=".absen.php" method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Nama Lengkap *</label>
                        <input type="text" name="nama" class="w-full px-4 py-3 bg-gray-50 rounded-lg border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Jenjang</label>
                        <select name="jenjang" class="w-full px-4 py-3 bg-gray-50 rounded-lg border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            <option>SD</option><option>SMP</option><option>SMA</option><option>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Nomer HP</label>
                        <input type="text" name="nomer_hp" class="w-full px-4 py-3 bg-gray-50 rounded-lg border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Apa alasan pengen belajar pemrograman?</label>
                        <textarea name="alasan" rows="3" class="w-full px-4 py-3 bg-gray-50 rounded-lg border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400"></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Verifikasi: <?php echo $num1; ?> + <?php echo $num2; ?>?</label>
                        <input type="number" name="captcha" class="w-full px-4 py-3 bg-gray-50 rounded-lg border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                        <?php if ($captchaError): ?>
                            <p class="text-red-500 text-xs mt-2"><?php echo htmlspecialchars($captchaError); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mt-8 text-right">
                    <button type="submit" class="bg-indigo-600 text-white font-bold px-8 py-3 rounded-lg hover:bg-indigo-700 shadow-lg transition-transform transform hover:scale-105">
                        Kirim Absensi
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>