<?php
session_start();
require_once '.config/config.php';

// Data Materi 1-20
$materi_list = array();
for ($i = 1; $i <= 20; $i++) {
    if ($i == 1) {
        $materi_list[$i] = array(
            'judul' => 'Pengenalan awal Logika - Pemrograman',
            'files' => array(
                array('name' => 'materi-1.html', 'url' => 'materi-1.html'),
                array('name' => 'tugas-1.html', 'url' => 'tugas-1.html')
            )
        );
    } else {
        $materi_list[$i] = array(
            'judul' => "Materi Pertemuan Ke-$i",
            'files' => array(
                array('name' => "materi-$i.html", 'url' => "materi-$i.html"),
                array('name' => "tugas-$i.html", 'url' => "tugas-$i.html")
            )
        );
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="https://nusabit.com/assets/logo-48.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nusabit Code Kitchen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0F172A; scroll-behavior: smooth; }
        .code-font { font-family: 'Fira Code', monospace; }
        .menu-card { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .menu-card:hover { transform: translateY(-8px); box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.3); }
        .action-button { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .action-button:hover { transform: translateY(-4px); box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3); }
        .custom-scrollbar::-webkit-scrollbar { height: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #1E293B; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: linear-gradient(90deg, #6366F1, #8B5CF6); border-radius: 10px; }
        .game-btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .game-btn:hover {
            transform: translateY(-4px) scale(1.02);
        }
        .hero-glow {
            animation: glow 3s ease-in-out infinite alternate;
        }
        @keyframes glow {
            0% { box-shadow: 0 0 20px rgba(99, 102, 241, 0.2); }
            100% { box-shadow: 0 0 40px rgba(139, 92, 246, 0.4); }
        }
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.5, 0, 0, 1);
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col text-gray-100">

    <!-- Animated Background -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-indigo-500/10 rounded-full blur-[100px] animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-violet-500/10 rounded-full blur-[100px] animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <!-- Header -->
    <header class="bg-dark-bg/80 backdrop-blur-xl border-b border-indigo-500/20 py-5 px-6 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <span class="text-white font-black text-sm">NK</span>
                </div>
                <div>
                    <h1 class="text-xl md:text-2xl font-extrabold tracking-tight">
                        pm.<span class="bg-gradient-to-r from-indigo-400 to-violet-400 bg-clip-text text-transparent">nusabit.com</span>
                    </h1>
                    <p class="text-[10px] text-gray-500 uppercase tracking-widest hidden md:block">Code Kitchen Edition</p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <a href="/admin/index.php" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg text-xs font-bold border border-gray-700 transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-halved text-indigo-400"></i> Admin
                </a>
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                    <span class="text-gray-300 text-sm hidden md:inline font-medium">
                        <i class="fa-regular fa-user text-indigo-400 mr-1"></i><?php echo htmlspecialchars($_SESSION['nama_panggilan']); ?>
                    </span>
                    <a href="logout.php" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 px-4 py-2 rounded-xl text-sm font-semibold border border-red-500/30 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="hidden md:inline">Logout</span>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white px-5 py-2 rounded-xl text-sm font-bold shadow-lg shadow-indigo-500/30 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span class="hidden md:inline">Login Member</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="flex-grow p-6 md:p-10 max-w-7xl mx-auto w-full relative z-10">

        <!-- Hero Section -->
        <div class="text-center mb-12 reveal active">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-indigo-500/10 rounded-full border border-indigo-500/20 text-indigo-400 text-xs font-bold mb-6">
                <i class="fa-solid fa-fire"></i> Belajar Koding Itu Seru!
            </div>
            <h2 class="text-4xl md:text-6xl font-black text-white mb-4 tracking-tight leading-tight">
                🍳 <span class="bg-gradient-to-r from-indigo-400 via-violet-400 to-purple-400 bg-clip-text text-transparent">Code Kitchen</span>
            </h2>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto leading-relaxed">
                Pilih "hidangan" materi koding hari ini, dari <span class="text-indigo-400 font-semibold">Algoritma Dasar</span> 
                hingga <span class="text-violet-400 font-semibold">Form & Interaksi</span>. 
                Semua disajikan dengan cara yang <span class="text-purple-400 font-semibold">asyik dan mudah dipahami</span>! 🚀
            </p>
        </div>

        <!-- Alert Success -->
        <?php if (isset($_SESSION['tugas_success'])): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-2xl mb-6 flex items-center gap-3" role="alert">
                <i class="fa-solid fa-check-circle text-emerald-400"></i>
                <p><?php echo $_SESSION['tugas_success']; ?></p>
            </div>
            <?php unset($_SESSION['tugas_success']); ?>
        <?php endif; ?>

        <!-- History Login -->
        <div class="mb-10 reveal">
            <a href=".historyabsen.php" class="action-button bg-gradient-to-r from-indigo-600 to-violet-600 text-white p-5 rounded-2xl shadow-lg flex items-center justify-between group">
                 <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center text-2xl">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div class="text-left">
                        <h3 class="text-lg font-bold flex items-center gap-2">
                            History Login
                            <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-300 text-[10px] font-bold rounded-full border border-emerald-500/30">Baru</span>
                        </h3>
                        <p class="text-indigo-200 text-sm">Lihat aktivitas login 3 hari terakhir</p>
                    </div>
                 </div>
                 <i class="fa-solid fa-chevron-right text-white/50 group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>

        <!-- Roadmap Section -->
        <div class="mb-12 reveal">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                    <span class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                        <i class="fa-solid fa-calendar-check text-white text-sm"></i>
                    </span>
                    Roadmap Belajar <span class="text-gray-500 text-sm font-normal ml-2">(20 Hari)</span>
                </h2>
                <span class="text-xs text-gray-500 hidden md:block">
                    <i class="fa-regular fa-eye mr-1"></i> Geser untuk lihat lebih →
                </span>
            </div>

            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            <div id="roadmap-container" class="flex overflow-x-auto pb-4 space-x-4 custom-scrollbar">
                <?php foreach ($materi_list as $hari => $data): ?>
                <?php $is_locked = ($hari > 3); ?>
                <div id="materi-card-<?php echo $hari; ?>" class="menu-card flex-shrink-0 w-64 <?php echo $is_locked ? 'bg-gray-800/50 border-gray-700/50' : 'bg-card-bg border-indigo-500/20 hover:border-indigo-500/40'; ?> border rounded-2xl shadow-lg overflow-hidden flex flex-col group">
                    <!-- Card Image -->
                    <div class="h-32 w-full relative overflow-hidden">
                        <img src="https://picsum.photos/seed/<?php echo $hari; ?>/320/160" alt="Menu Hari <?php echo $hari; ?>" class="w-full h-full object-cover <?php echo $is_locked ? 'grayscale opacity-30' : 'group-hover:scale-105 transition-transform duration-500'; ?>">
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent"></div>
                        <div class="absolute top-3 left-3 flex gap-2">
                            <span class="px-3 py-1 <?php echo $is_locked ? 'bg-gray-700 text-gray-400' : 'bg-indigo-500/80 text-white'; ?> text-[10px] font-bold rounded-full backdrop-blur-sm">
                                #<?php echo $hari; ?>
                            </span>
                        </div>
                        <div class="absolute bottom-3 right-3">
                            <i class="fa-solid <?php echo $is_locked ? 'fa-lock text-gray-500' : 'fa-book-open text-white/70'; ?> text-lg"></i>
                        </div>
                    </div>
                    
                    <!-- Card Content -->
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="font-bold <?php echo $is_locked ? 'text-gray-500' : 'text-white'; ?> text-sm mb-4 leading-relaxed min-h-[2.5rem]">
                            <?php echo $data['judul']; ?>
                        </h3>
                        <div class="space-y-2 mt-auto">
                            <?php foreach ($data['files'] as $file): ?>
                            <?php if ($is_locked): ?>
                            <div class="flex items-center text-xs text-gray-500 font-medium cursor-not-allowed">
                                <i class="fa-solid fa-lock mr-2 text-gray-600"></i>
                                <span class="truncate"><?php echo $file['name']; ?></span>
                            </div>
                            <?php else: ?>
                            <a href="<?php echo $file['url']; ?>" class="flex items-center text-xs text-indigo-400 hover:text-indigo-300 font-medium transition-colors group/link">
                                <i class="fa-solid fa-file-code mr-2 text-indigo-500/70 group-hover/link:text-indigo-400"></i>
                                <span class="truncate"><?php echo $file['name']; ?></span>
                                <i class="fa-solid fa-arrow-up-right-from-square ml-auto opacity-0 group-hover/link:opacity-100 transition-opacity text-[8px]"></i>
                            </a>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Playground & Games Section -->
            <div class="mt-10 reveal">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                        <i class="fa-solid fa-gamepad text-white text-sm"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">Playground & Games</h3>
                    <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold rounded-full border border-emerald-500/30">Seru!</span>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                    <a href="typemaster.html" class="game-btn flex flex-col items-center gap-2 p-4 bg-gray-800/50 rounded-2xl border border-gray-700/50 hover:border-indigo-500/30 hover:bg-gray-800 transition-all text-center group">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center text-xl shadow-lg group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-keyboard text-white"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-300 group-hover:text-white transition-colors">Typing Master</span>
                        <span class="text-[9px] text-gray-500">10 Jari</span>
                    </a>

                    <a href="simonwarna.html" class="game-btn flex flex-col items-center gap-2 p-4 bg-gray-800/50 rounded-2xl border border-gray-700/50 hover:border-indigo-500/30 hover:bg-gray-800 transition-all text-center group">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center text-xl shadow-lg group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-brain text-white"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-300 group-hover:text-white transition-colors">Simon Warna</span>
                        <span class="text-[9px] text-gray-500">Latihan Ingatan</span>
                    </a>

                    <a href="flipcard.html" class="game-btn flex flex-col items-center gap-2 p-4 bg-gray-800/50 rounded-2xl border border-gray-700/50 hover:border-indigo-500/30 hover:bg-gray-800 transition-all text-center group">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center text-xl shadow-lg group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-layer-group text-white"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-300 group-hover:text-white transition-colors">Flip Card</span>
                        <span class="text-[9px] text-gray-500">Memory Game</span>
                    </a>

                    <a href="minesweeper.html" class="game-btn flex flex-col items-center gap-2 p-4 bg-gray-800/50 rounded-2xl border border-gray-700/50 hover:border-indigo-500/30 hover:bg-gray-800 transition-all text-center group">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-500 rounded-xl flex items-center justify-center text-xl shadow-lg group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-bomb text-white"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-300 group-hover:text-white transition-colors">Minesweeper</span>
                        <span class="text-[9px] text-gray-500">Classic Game</span>
                    </a>

                    <a href="playground-1.html" class="game-btn flex flex-col items-center gap-2 p-4 bg-gradient-to-br from-indigo-600/30 to-violet-600/30 rounded-2xl border border-indigo-500/30 hover:border-indigo-400/50 transition-all text-center group col-span-2 md:col-span-1">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-violet-500 rounded-xl flex items-center justify-center text-xl shadow-lg shadow-indigo-500/30 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-laptop-code text-white"></i>
                        </div>
                        <span class="text-xs font-bold text-indigo-300 group-hover:text-white transition-colors">Playground ✨</span>
                        <span class="text-[9px] text-gray-500">Latihan Koding</span>
                    </a>
                </div>
            </div>

            <?php else: ?>
            <!-- Locked State -->
            <div class="bg-gray-800/50 border border-gray-700/50 rounded-3xl p-12 text-center shadow-lg backdrop-blur-sm">
                <div class="w-20 h-20 bg-gradient-to-br from-indigo-500/20 to-violet-500/20 rounded-full flex items-center justify-center mx-auto mb-5 text-3xl border border-indigo-500/20">
                    <i class="fa-solid fa-lock text-indigo-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-3">Materi Terkunci 🔒</h3>
                <p class="text-gray-400 mb-8 max-w-md mx-auto">Silakan login sebagai member untuk mengakses roadmap belajar lengkap dan semua materi seru!</p>
                <a href="login.php" class="inline-flex items-center gap-3 px-8 py-3.5 bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold rounded-2xl hover:from-indigo-500 hover:to-violet-500 transition-all shadow-lg shadow-indigo-500/30">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Login Member
                </a>
            </div>
            <?php endif; ?>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-900/80 backdrop-blur-sm border-t border-gray-800 text-gray-400 py-10 px-6 mt-auto">
        <div class="max-w-6xl mx-auto text-center">
            <div class="flex justify-center gap-6 mb-6 text-lg">
                <i class="fa-brands fa-html5 text-orange-500"></i>
                <i class="fa-brands fa-css3-alt text-blue-500"></i>
                <i class="fa-brands fa-js text-yellow-500"></i>
                <i class="fa-brands fa-php text-purple-500"></i>
            </div>
            <p class="text-xs uppercase tracking-widest mb-1 text-gray-500">&copy; 2026 Nusabit.com - Kitchen Edition</p>
            <p class="text-xs text-gray-600">BRQ DAGMAR FRAHADIXTA • 089-7925-1919 • brqdagmar@gmail.com</p>
        </div>
    </footer>

    <script>
        // Scroll Animation
        document.addEventListener("DOMContentLoaded", function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) entry.target.classList.add('active');
                });
            }, { threshold: 0.1 });
            document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

            // Auto-scroll ke materi card 9
            const targetIds = ['materi-card-3'];
            targetIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            });
        });
    </script>

</body>
</html>

