<?php
session_start();
require_once '.config/config.php';

$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
    $string = array('y' => 'tahun', 'm' => 'bulan', 'w' => 'minggu', 'd' => 'hari', 'h' => 'jam', 'i' => 'menit', 's' => 'detik');
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
        } else {
            unset($string[$k]);
        }
    }
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' yang lalu' : 'baru saja';
}

$limit = 20;
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($page < 1) $page = 1;
$start = ($page - 1) * $limit;

$totalResult = mysqli_query($conn, "SELECT COUNT(*) as total FROM absensi WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 3 DAY)");
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);

$sql = "SELECT * FROM absensi WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 3 DAY) ORDER BY id DESC LIMIT $start, $limit";
$result = mysqli_query($conn, $sql);

$entries = array();
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $row['relative_time'] = time_elapsed_string($row['timestamp']);
        $entries[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="https://nusabit.com/assets/logo-48.png">
    <meta http-equiv="refresh" content="5">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Absen - Nusabit Code Kitchen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Space+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="min-h-screen">
     <header class="bg-white border-b border-gray-100 py-12 px-6 shadow-sm">
        <div class="max-w-6xl mx-auto text-center">
            <a href="index.php" class="text-gray-500 hover:text-indigo-600 mb-8 inline-block"><i class="fa-solid fa-arrow-left"></i> Kembali ke Menu</a>
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4 tracking-tight">
                History <span class="text-indigo-600 italic">Absensi.</span>
            </h1>
        </div>
    </header>

    <main class="p-6 md:p-12 max-w-4xl mx-auto w-full">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col h-full">
            <div class="flex-grow space-y-6">
                <?php if (!empty($entries)): ?>
                    <?php foreach ($entries as $row): ?>
                        <div class="flex items-start space-x-4 border-b border-gray-50 pb-4 last:border-0">
                            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-full flex-shrink-0 flex items-center justify-center font-bold text-sm">
                                <?php echo strtoupper(substr($row['nama'], 0, 1)); ?>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-gray-800 truncate"><?php echo htmlspecialchars($row['nama']); ?></p>
                                <p class="text-[10px] text-gray-400 uppercase"><?php echo $row['relative_time']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-400 text-center py-10">Belum ada history.</p>
                <?php endif; ?>
            </div>

            <?php if (isset($totalPages) && $totalPages > 1): ?>
            <div class="mt-8 pt-4 border-t border-gray-100 flex justify-between items-center text-xs">
                <?php if ($page > 1): ?>
                    <a href="?p=<?php echo $page - 1; ?>" class="px-3 py-1 bg-gray-100 hover:bg-indigo-600 hover:text-white rounded transition-colors">Prev</a>
                <?php else: ?>
                    <span class="px-3 py-1 text-gray-300">Prev</span>
                <?php endif; ?>

                <span class="text-gray-500 font-medium">Halaman <?php echo $page; ?> dari <?php echo $totalPages; ?></span>

                <?php if ($page < $totalPages): ?>
                    <a href="?p=<?php echo $page + 1; ?>" class="px-3 py-1 bg-gray-100 hover:bg-indigo-600 hover:text-white rounded transition-colors">Next</a>
                <?php else: ?>
                    <span class="px-3 py-1 text-gray-300">Next</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <div class="mt-6 bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center">
            <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fa-solid fa-user-astronaut"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Bergabung dengan Code Kitchen</h2>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">Akses materi lengkap, kerjakan tugas, dan pantau progres belajar kamu dengan akun member.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="login.php" class="inline-flex justify-center items-center px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-lg hover:shadow-indigo-500/30">
                    <i class="fa-solid fa-right-to-bracket mr-2"></i>Login Member
                </a>
                <a href="register.php" class="inline-flex justify-center items-center px-6 py-3 bg-white border-2 border-gray-100 text-gray-700 font-bold rounded-xl hover:border-indigo-100 hover:bg-indigo-50 transition-all">
                    <i class="fa-solid fa-user-plus mr-2"></i>Daftar Baru
                </a>
            </div>
        </div>
    </main>

</body>
</html>