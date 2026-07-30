<?php
session_start();
$root_path = dirname(__DIR__);
require_once $root_path . '/.config/config.php';
require_once $root_path . '/.config/password.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: ../login.php');
    exit;
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = ''; $message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    if (isset($_POST['action']) && $_POST['action'] === 'change_password' && isset($_POST['id']) && isset($_POST['new_password'])) {
        $id = (int)$_POST['id'];
        $raw_pw = trim($_POST['new_password']);
        if (!empty($raw_pw)) {
            $hash = password_hash($raw_pw, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'si', $hash, $id);
            mysqli_stmt_execute($stmt);
            $affected = mysqli_affected_rows($conn);
            mysqli_stmt_close($stmt);
            
            if ($affected > 0) {
                $message = "✅ Password user ID $id diubah! Gunakan: '$raw_pw'";
                $message_type = 'success';
            } else {
                $message = "❌ Gagal update user ID $id";
                $message_type = 'error';
            }
        } else {
            $message = "❌ Password kosong";
            $message_type = 'error';
        }
    }
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC LIMIT 20");
$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['password'] = '***';
    $users[] = $row;
}
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];

// Fetch quiz answers for admin view
$quiz_result = mysqli_query($conn, "
    SELECT qa.*, u.nama_lengkap, u.username 
    FROM quiz_answers qa 
    LEFT JOIN users u ON qa.user_id = u.id 
    ORDER BY qa.submitted_at DESC 
    LIMIT 50
");
$quiz_answers = [];
while ($row = mysqli_fetch_assoc($quiz_result)) {
    $quiz_answers[] = $row;
}
$total_quiz = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM quiz_answers"))['total'];

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Users</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-4 max-w-6xl mx-auto bg-gray-50">
    
    <div class="bg-white shadow-sm rounded-lg p-4 mb-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">Kelola User (<?php echo $total; ?>)</h1>
        <a href="../index.php" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm font-bold">← Kembali</a>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button name="logout" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded text-sm font-bold">Logout</button>
        </form>
    </div>
    
    <?php if ($message): ?>
        <div class="p-4 mb-4 rounded-lg border <?php echo $message_type=='success' ? 'bg-green-50 border-green-300 text-green-800' : 'bg-red-50 border-red-300 text-red-800'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-3 text-left font-bold text-gray-700">ID</th>
                    <th class="px-3 py-3 text-left font-bold text-gray-700">Username</th>
                    <th class="px-3 py-3 text-left font-bold text-gray-700">Nama</th>
                    <th class="px-3 py-3 text-left font-bold text-gray-700">Role</th>
                    <th class="px-3 py-3 text-left font-bold text-gray-700">Email</th>
                    <th class="px-3 py-3 text-center font-bold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($users as $u): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-3 py-2 font-mono"><?php echo $u['id']; ?></td>
                    <td class="px-3 py-2 font-semibold"><?php echo htmlspecialchars($u['username']); ?></td>
                    <td class="px-3 py-2"><?php echo htmlspecialchars($u['nama_lengkap']); ?></td>
                    <td class="px-3 py-2">
                        <span class="px-2 py-1 rounded text-xs font-bold <?php echo $u['role']=='admin' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?>">
                            <?php echo ucfirst($u['role']); ?>
                        </span>
                    </td>
                    <td class="px-3 py-2 text-gray-600"><?php echo htmlspecialchars($u['email']); ?></td>
                    <td class="px-3 py-2 text-center">
                        <div class="flex gap-1 justify-center">
                            <button onclick="changePass(<?php echo $u['id']; ?>)" class="px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded font-bold">Password</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?><tr><td colspan="6" class="p-8 text-center text-gray-500">No users</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Quiz Answers Section -->
    <div class="mt-8 bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="flex items-center justify-between p-4 bg-gray-50 border-b">
            <h2 class="text-lg font-bold">📋 Jawaban Tugas (<?php echo $total_quiz; ?>)</h2>
            <span class="text-xs text-gray-500">50 terbaru</span>
        </div>
        <?php if (empty($quiz_answers)): ?>
            <p class="p-8 text-center text-gray-500">Belum ada jawaban tugas.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-2 py-2 text-left font-bold text-gray-600">#</th>
                            <th class="px-2 py-2 text-left font-bold text-gray-600">User</th>
                            <th class="px-2 py-2 text-left font-bold text-gray-600">Materi</th>
                            <th class="px-2 py-2 text-left font-bold text-gray-600">Jawaban 1</th>
                            <th class="px-2 py-2 text-left font-bold text-gray-600">Jawaban 2</th>
                            <th class="px-2 py-2 text-left font-bold text-gray-600">Jawaban 3</th>
                            <th class="px-2 py-2 text-left font-bold text-gray-600">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($quiz_answers as $qa): ?>
                        <tr class="hover:bg-blue-50">
                            <td class="px-2 py-2 font-mono text-gray-400"><?php echo $qa['id']; ?></td>
                            <td class="px-2 py-2 font-semibold"><?php echo htmlspecialchars($qa['nama_panggilan'] ?: ($qa['username'] ?: '—')); ?></td>
                            <td class="px-2 py-2">
                                <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-800 rounded font-bold"><?php echo htmlspecialchars($qa['materi_soal']); ?></span>
                            </td>
                            <td class="px-2 py-2 max-w-[200px] truncate"><?php echo htmlspecialchars($qa['jawaban_1']); ?></td>
                            <td class="px-2 py-2 max-w-[200px] truncate"><?php echo htmlspecialchars($qa['jawaban_2']); ?></td>
                            <td class="px-2 py-2 max-w-[150px] truncate"><?php echo htmlspecialchars($qa['jawaban_3']); ?></td>
                            <td class="px-2 py-2 text-gray-500 whitespace-nowrap"><?php echo date('d M H:i', strtotime($qa['submitted_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Compact Password Modal -->
    <div id="pwModal" class="fixed inset-0 bg-black/60 hidden flex items-center justify-center z-50 p-4" onclick="closePw()">
        <div class="bg-white rounded-lg p-6 w-full max-w-sm shadow-xl border" onclick="event.stopPropagation()">
            <h3 class="text-lg font-bold mb-4">Password ID <span id="pwId"></span></h3>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="change_password">
                <input type="hidden" name="id" id="formPwId">
                <div class="mb-4">
                    <input type="password" name="new_password" required placeholder="Password baru" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-2 text-xs text-gray-600 mb-4 p-2 bg-blue-50 rounded">
                    Plain password → auto bcrypt hash
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="closePw()" class="flex-1 p-2 bg-gray-200 hover:bg-gray-300 rounded text-sm font-bold">Batal</button>
                    <button type="submit" class="flex-1 p-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-sm font-bold">Ubah</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function changePass(id) {
            document.getElementById('pwId').textContent = id;
            document.getElementById('formPwId').value = id;
            document.getElementById('pwModal').classList.remove('hidden');
        }
        function closePw() { document.getElementById('pwModal').classList.add('hidden'); }
    </script>

</body>
</html>
