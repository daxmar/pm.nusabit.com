<?php
require_once '.config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $materi_soal = isset($_POST['materi_soal']) ? $_POST['materi_soal'] : '';

    if ($password === 'dagmar') {
        $sql = "SELECT u.nama_lengkap, q.nama_panggilan, q.jawaban_1, q.jawaban_2, q.jawaban_3, q.submitted_at 
                FROM quiz_answers q 
                LEFT JOIN users u ON q.user_id = u.id 
                WHERE q.materi_soal = ? 
                ORDER BY q.submitted_at DESC";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $materi_soal);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) > 0) {
                echo '<div class="overflow-x-auto custom-scroll">';
                echo '<table class="w-full text-left border-collapse">';
                echo '<thead>
                        <tr class="text-gray-400 border-b border-gray-700 text-sm uppercase tracking-wider">
                            <th class="p-4 font-semibold">Peserta</th>
                            <th class="p-4 font-semibold">Waktu</th>
                            <th class="p-4 font-semibold w-1/4">Soal 1</th>
                            <th class="p-4 font-semibold w-1/4">Soal 2</th>
                            <th class="p-4 font-semibold w-1/4">Soal 3</th>
                        </tr>
                      </thead>';
                echo '<tbody class="text-gray-300 text-sm">';
                
                while ($row = mysqli_fetch_assoc($result)) {
                    $nama = !empty($row['nama_lengkap']) ? htmlspecialchars($row['nama_lengkap']) : htmlspecialchars($row['nama_panggilan']);
                    $waktu = date('d M, H:i:s', strtotime($row['submitted_at']));
                    
                    echo '<tr class="border-b border-gray-800 hover:bg-gray-800/50 transition-colors">';
                    echo '<td class="p-4 align-top">
                            <div class="font-bold text-white mb-1">' . $nama . '</div>
                            <div class="text-xs text-primary">' . htmlspecialchars($row['nama_panggilan']) . '</div>
                          </td>';
                    echo '<td class="p-4 align-top whitespace-nowrap text-gray-500">' . $waktu . '</td>';
                    echo '<td class="p-4 align-top"><div class="max-h-32 overflow-y-auto bg-gray-900/50 p-2 rounded border border-gray-700">' . nl2br(htmlspecialchars($row['jawaban_1'])) . '</div></td>';
                    echo '<td class="p-4 align-top"><div class="max-h-32 overflow-y-auto bg-gray-900/50 p-2 rounded border border-gray-700">' . nl2br(htmlspecialchars($row['jawaban_2'])) . '</div></td>';
                    echo '<td class="p-4 align-top"><div class="max-h-32 overflow-y-auto bg-gray-900/50 p-2 rounded border border-gray-700">' . nl2br(htmlspecialchars($row['jawaban_3'])) . '</div></td>';
                    echo '</tr>';
                }
                echo '</tbody></table></div>';
            } else {
                echo '<div class="text-center py-12 text-gray-500"><i class="fas fa-inbox text-4xl mb-4 opacity-50"></i><p>Belum ada jawaban.</p></div>';
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        echo 'Password salah';
    }
}
mysqli_close($conn);
?>