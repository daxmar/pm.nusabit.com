<?php
date_default_timezone_set('Asia/Jakarta');
$logFile = __DIR__ . '/api/quiz-results.json';
$results = [];

if (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    $results = json_decode($content, true) ?: [];
    $results = array_reverse($results);
}

function formatTanggalIndo($timestamp) {
    $date = new DateTime($timestamp, new DateTimeZone('UTC'));
    $date->setTimezone(new DateTimeZone('Asia/Jakarta'));
    
    $bulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    
    $hariNama = $hari[$date->format('w')];
    $tgl = $date->format('d');
    $bln = $bulan[intval($date->format('m')) - 1];
    $thn = $date->format('Y');
    $waktu = $date->format('H:i');
    
    return "$hariNama, $tgl $bln $thn - $waktu WIB";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="https://nusabit.com/assets/logo-48.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="10">
    <title>Hasil Quiz - Tugas 6</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card { background: rgba(255,255,255,0.1); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.3); }
        .live-dot { animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 text-white min-h-screen p-8">
    
    <nav class="max-w-6xl mx-auto mb-12 flex justify-between items-center bg-white/10 backdrop-blur-2xl rounded-3xl p-6 border border-white/30 shadow-2xl">
        <a href="tugas-6.html" class="text-2xl font-black bg-gradient-to-r from-amber-400 to-orange-400 bg-clip-text flex items-center gap-4">
            <i class="fas fa-arrow-left"></i> Kembali ke Tugas 6
        </a>
        <a href="index.php" class="text-lg font-bold px-6 py-3 bg-white/20 hover:bg-white/30 rounded-2xl border border-white/30 transition-all">
            <i class="fas fa-home mr-2"></i> Home
        </a>
    </nav>

    <main class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-5xl font-black mb-4 bg-gradient-to-r from-emerald-400 to-teal-400 bg-clip-text text-transparent">
                <i class="fas fa-chart-bar mr-4"></i>Hasil Quiz Tugas 6
            </h1>
            <p class="text-xl text-white/80">Daftar siswa yang telah menyelesaikan Review Quiz Materi 1-6</p>
            <div class="mt-6 flex flex-wrap justify-center gap-4">
                <div class="inline-flex items-center gap-3 px-6 py-3 bg-white/10 rounded-2xl border border-white/20">
                    <i class="fas fa-users text-emerald-400 text-2xl"></i>
                    <span class="text-2xl font-bold"><?= count($results) ?></span>
                    <span class="text-lg text-white/70">siswa telah mengerjakan</span>
                </div>
                <div class="inline-flex items-center gap-3 px-6 py-3 bg-emerald-500/20 rounded-2xl border border-emerald-400/50">
                    <span class="w-3 h-3 bg-emerald-400 rounded-full live-dot"></span>
                    <span class="text-lg font-bold text-emerald-400">Auto-refresh setiap 10 detik</span>
                </div>
            </div>
        </div>

        <?php if (empty($results)): ?>
            <div class="glass-card p-16 rounded-4xl text-center border border-white/20 bg-white/5">
                <i class="fas fa-inbox text-8xl text-white/30 mb-8 block"></i>
                <h2 class="text-3xl font-bold text-white/70 mb-4">Passwordnya adalah hasil dari 50+25+25 = </h2>
                <p class="text-xl text-white/50">Hasil quiz akan muncul di sini setelah siswa menyelesaikan tugas.</p>
            </div>
        <?php else: ?>
            <div class="glass-card rounded-4xl border border-white/20 bg-white/5 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-white/10">
                            <tr>
                                <th class="px-6 py-5 text-left text-lg font-bold text-emerald-400">#</th>
                                <th class="px-6 py-5 text-left text-lg font-bold text-emerald-400"><i class="fas fa-user mr-2"></i>Nama</th>
                                <th class="px-6 py-5 text-center text-lg font-bold text-emerald-400"><i class="fas fa-star mr-2"></i>Nilai</th>
                                <th class="px-6 py-5 text-center text-lg font-bold text-emerald-400"><i class="fas fa-clock mr-2"></i>Waktu</th>
                                <th class="px-6 py-5 text-left text-lg font-bold text-emerald-400"><i class="fas fa-calendar mr-2"></i>Tanggal</th>
                                <th class="px-6 py-5 text-center text-lg font-bold text-emerald-400"><i class="fas fa-trophy mr-2"></i>Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            <?php foreach ($results as $index => $r): ?>
                                <tr class="hover:bg-white/5 transition-all">
                                    <td class="px-6 py-5 text-white/60 font-semibold"><?= $index + 1 ?></td>
                                    <td class="px-6 py-5 font-bold text-lg"><?= htmlspecialchars($r['nama']) ?></td>
                                    <td class="px-6 py-5 text-center">
                                        <?php 
                                        $nilai = intval($r['nilai']);
                                        $badgeColor = $nilai >= 90 ? 'bg-yellow-500/20 text-yellow-400 border-yellow-400/50' : 
                                                     ($nilai >= 70 ? 'bg-emerald-500/20 text-emerald-400 border-emerald-400/50' : 
                                                     ($nilai >= 50 ? 'bg-blue-500/20 text-blue-400 border-blue-400/50' : 
                                                     'bg-red-500/20 text-red-400 border-red-400/50'));
                                        ?>
                                        <span class="inline-flex items-center px-4 py-2 rounded-xl font-black text-xl border <?= $badgeColor ?>">
                                            <?= $nilai ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-center text-white/80 font-semibold">
                                        <i class="fas fa-stopwatch mr-2 text-amber-400"></i><?= htmlspecialchars($r['waktu']) ?>
                                    </td>
                                    <td class="px-6 py-5 text-white/70">
                                        <?= formatTanggalIndo($r['timestamp']) ?>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <?php if ($nilai >= 90): ?>
                                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500/20 text-yellow-400 rounded-xl border border-yellow-400/50 font-bold">
                                                <i class="fas fa-crown"></i> Juara!
                                            </span>
                                        <?php elseif ($nilai >= 70): ?>
                                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500/20 text-emerald-400 rounded-xl border border-emerald-400/50 font-bold">
                                                <i class="fas fa-check-circle"></i> Bagus
                                            </span>
                                        <?php elseif ($nilai >= 50): ?>
                                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500/20 text-blue-400 rounded-xl border border-blue-400/50 font-bold">
                                                <i class="fas fa-thumbs-up"></i> Cukup
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-red-500/20 text-red-400 rounded-xl border border-red-400/50 font-bold">
                                                <i class="fas fa-redo"></i> Perlu Latihan
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
                <?php
                $totalStudents = count($results);
                $avgScore = $totalStudents > 0 ? round(array_sum(array_column($results, 'nilai')) / $totalStudents) : 0;
                $highestScore = $totalStudents > 0 ? max(array_column($results, 'nilai')) : 0;
                $passCount = count(array_filter($results, fn($r) => intval($r['nilai']) >= 70));
                ?>
                <div class="glass-card p-6 rounded-3xl border border-white/20 bg-white/5 text-center">
                    <i class="fas fa-users text-4xl text-blue-400 mb-4 block"></i>
                    <div class="text-4xl font-black"><?= $totalStudents ?></div>
                    <div class="text-white/70 font-semibold">Total Siswa</div>
                </div>
                <div class="glass-card p-6 rounded-3xl border border-white/20 bg-white/5 text-center">
                    <i class="fas fa-chart-line text-4xl text-emerald-400 mb-4 block"></i>
                    <div class="text-4xl font-black"><?= $avgScore ?></div>
                    <div class="text-white/70 font-semibold">Rata-rata Nilai</div>
                </div>
                <div class="glass-card p-6 rounded-3xl border border-white/20 bg-white/5 text-center">
                    <i class="fas fa-trophy text-4xl text-yellow-400 mb-4 block"></i>
                    <div class="text-4xl font-black"><?= $highestScore ?></div>
                    <div class="text-white/70 font-semibold">Nilai Tertinggi</div>
                </div>
                <div class="glass-card p-6 rounded-3xl border border-white/20 bg-white/5 text-center">
                    <i class="fas fa-check-double text-4xl text-purple-400 mb-4 block"></i>
                    <div class="text-4xl font-black"><?= $passCount ?></div>
                    <div class="text-white/70 font-semibold">Tuntas (≥70)</div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer class="mt-16 py-8 text-center text-white/50 text-lg border-t border-white/20">
        <p>Nusabit Code Kitchen © 2026 | Hasil Quiz Tugas 6</p>
        <p class="mt-2 text-sm text-white/40">
            <i class="fas fa-clock mr-2"></i>Waktu saat ini: <?= formatTanggalIndo('now') ?>
        </p>
    </footer>
</body>
</html>
