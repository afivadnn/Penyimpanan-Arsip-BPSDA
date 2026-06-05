<?php 
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$active_menu = 'dashboard';

if (strpos($request_uri, '/arsip/') !== false) {
    $active_menu = 'arsip';
} elseif (strpos($request_uri, '/peminjaman/') !== false) {
    $active_menu = 'peminjaman';
} elseif (strpos($request_uri, '/users/') !== false) {
    $active_menu = 'users';
}
?>

<aside id="sidebar" 
       class="fixed inset-y-0 left-0 z-50 w-72 
              bg-gradient-to-b from-indigo-600 via-purple-600 to-indigo-700
              transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out
              shadow-2xl shadow-purple-900/30">
    
    <div class="flex flex-col h-full">
        
        <!-- Header -->
        <div class="relative px-6 py-6 border-b border-white/10">
            <!-- Subtle pattern overlay -->
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(255,255,255,0.1),transparent_50%)]"></div>
            
            <div class="relative flex items-center gap-4">
                <div class="flex-shrink-0 w-14 h-14 bg-white/10 backdrop-blur-sm
                            rounded-xl p-2.5 shadow-lg shadow-black/20 ring-2 ring-white/20 
                            border border-white/10">
                    <img src="<?= BASE_URL ?>assets/img/logo.png" 
                         alt="Logo" 
                         class="w-full h-full object-contain drop-shadow-lg">
                </div>
                
                <div class="flex-1 min-w-0">
                    <h1 class="text-xl font-bold text-white tracking-tight truncate drop-shadow-sm">E-ARSIP</h1>
                    <p class="text-xs text-indigo-200 truncate mt-0.5">PSDA Serayu Bogowonto</p>
                </div>
                
                <button onclick="closeSidebar()" 
                        class="md:hidden flex-shrink-0 w-8 h-8 flex items-center justify-center 
                               rounded-lg hover:bg-white/10 transition-colors">
                    <i class="fas fa-times text-white/80"></i>
                </button>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 overflow-y-auto space-y-2">
            
            <a href="<?= BASE_URL ?>views/dashboard.php"
               class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl 
                      transition-all duration-200
                      <?= $active_menu === 'dashboard' 
                          ? 'bg-white text-indigo-700 shadow-lg shadow-black/10' 
                          : 'text-white/90 hover:bg-white/10 hover:text-white' ?>">
                
                <?php if ($active_menu === 'dashboard'): ?>
                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-10 bg-indigo-600 rounded-r-full shadow-lg shadow-indigo-600/50"></span>
                <?php endif; ?>
                
                <div class="relative z-10 flex items-center justify-center w-5 
                            <?= $active_menu === 'dashboard' ? 'text-indigo-600' : 'text-white/80 group-hover:text-white' ?>">
                    <i class="fas fa-home text-base transition-transform duration-200 
                              <?= $active_menu === 'dashboard' ? 'scale-110' : 'group-hover:scale-110' ?>"></i>
                </div>
                
                <span class="relative z-10 font-semibold text-sm">Dashboard</span>
                
                <!-- Shine effect on hover -->
                <?php if ($active_menu !== 'dashboard'): ?>
                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <div class="absolute inset-0 bg-gradient-to-r from-white/5 to-transparent rounded-xl"></div>
                </div>
                <?php endif; ?>
            </a>

            <a href="<?= BASE_URL ?>views/arsip/index.php"
               class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl 
                      transition-all duration-200
                      <?= $active_menu === 'arsip' 
                          ? 'bg-white text-indigo-700 shadow-lg shadow-black/10' 
                          : 'text-white/90 hover:bg-white/10 hover:text-white' ?>">
                
                <?php if ($active_menu === 'arsip'): ?>
                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-10 bg-indigo-600 rounded-r-full shadow-lg shadow-indigo-600/50"></span>
                <?php endif; ?>
                
                <div class="relative z-10 flex items-center justify-center w-5 
                            <?= $active_menu === 'arsip' ? 'text-indigo-600' : 'text-white/80 group-hover:text-white' ?>">
                    <i class="fas fa-folder-open text-base transition-transform duration-200 
                              <?= $active_menu === 'arsip' ? 'scale-110' : 'group-hover:scale-110' ?>"></i>
                </div>
                <span class="relative z-10 font-semibold text-sm">Data Arsip</span>
                
                <?php if ($active_menu !== 'arsip'): ?>
                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <div class="absolute inset-0 bg-gradient-to-r from-white/5 to-transparent rounded-xl"></div>
                </div>
                <?php endif; ?>
            </a>

            <a href="<?= BASE_URL ?>views/peminjaman/index.php"
               class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl 
                      transition-all duration-200
                      <?= $active_menu === 'peminjaman' 
                          ? 'bg-white text-indigo-700 shadow-lg shadow-black/10' 
                          : 'text-white/90 hover:bg-white/10 hover:text-white' ?>">
                
                <?php if ($active_menu === 'peminjaman'): ?>
                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-10 bg-indigo-600 rounded-r-full shadow-lg shadow-indigo-600/50"></span>
                <?php endif; ?>
                
                <div class="relative z-10 flex items-center justify-center w-5 
                            <?= $active_menu === 'peminjaman' ? 'text-indigo-600' : 'text-white/80 group-hover:text-white' ?>">
                    <i class="fas fa-hand-holding text-base transition-transform duration-200 
                              <?= $active_menu === 'peminjaman' ? 'scale-110' : 'group-hover:scale-110' ?>"></i>
                </div>
                <span class="relative z-10 font-semibold text-sm">Peminjaman</span>
                
                <?php if ($active_menu !== 'peminjaman'): ?>
                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <div class="absolute inset-0 bg-gradient-to-r from-white/5 to-transparent rounded-xl"></div>
                </div>
                <?php endif; ?>
            </a>

            <?php if (empty($_SESSION['user_subag_id'])): ?>
                <div class="pt-4 pb-2">
                    <div class="flex items-center gap-3 px-4">
                        <div class="h-px flex-1 bg-white/20"></div>
                        <span class="text-xs font-bold text-white/60 uppercase tracking-wider">Admin</span>
                        <div class="h-px flex-1 bg-white/20"></div>
                    </div>
                </div>

                <a href="<?= BASE_URL ?>views/users/index.php"
                   class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl 
                          transition-all duration-200
                          <?= $active_menu === 'users' 
                              ? 'bg-white text-indigo-700 shadow-lg shadow-black/10' 
                              : 'text-white/90 hover:bg-white/10 hover:text-white' ?>">
                    
                    <?php if ($active_menu === 'users'): ?>
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-10 bg-indigo-600 rounded-r-full shadow-lg shadow-indigo-600/50"></span>
                    <?php endif; ?>
                    
                    <div class="relative z-10 flex items-center justify-center w-5 
                                <?= $active_menu === 'users' ? 'text-indigo-600' : 'text-white/80 group-hover:text-white' ?>">
                        <i class="fas fa-users-cog text-base transition-transform duration-200 
                                  <?= $active_menu === 'users' ? 'scale-110' : 'group-hover:scale-110' ?>"></i>
                    </div>
                    <span class="relative z-10 font-semibold text-sm">Manajemen User</span>
                    
                    <?php if ($active_menu !== 'users'): ?>
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="absolute inset-0 bg-gradient-to-r from-white/5 to-transparent rounded-xl"></div>
                    </div>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        </nav>

        <!-- Logout + Footer -->
        <div class="border-t border-white/10 bg-gradient-to-b from-transparent to-black/20">
            <div class="p-4">
                <a href="<?= BASE_URL ?>logout.php"
                   onclick="return confirm('Yakin ingin keluar dari sistem?');"
                   class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl 
                          bg-red-500/20 hover:bg-red-500/30
                          text-red-200 hover:text-white
                          transition-all duration-200 
                          border border-red-400/30 hover:border-red-400/50">
                    
                    <div class="relative z-10 flex items-center justify-center w-5">
                        <i class="fas fa-sign-out-alt text-base transition-transform duration-200 group-hover:translate-x-0.5"></i>
                    </div>
                    <span class="relative z-10 font-semibold text-sm">Keluar</span>
                    
                    <i class="fas fa-arrow-right text-xs ml-auto opacity-0 -translate-x-2 
                              group-hover:opacity-100 group-hover:translate-x-0 
                              transition-all duration-200"></i>
                </a>
            </div>

            <div class="px-6 py-4 text-center border-t border-white/10">
                <p class="text-xs text-white/60">
                    © <?= date('Y') ?> PSDA Serayu Citanduy
                </p>
                <p class="text-xs text-white/50 mt-1.5">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/10 border border-white/20">
                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse shadow-sm shadow-green-400/50"></span>
                        <span class="font-medium">v2.0</span>
                    </span>
                </p>
            </div>
        </div>
    </div>
</aside>

<div id="overlay" 
     class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 
            opacity-0 pointer-events-none md:hidden
            transition-opacity duration-300"
     onclick="closeSidebar()"></div>

<script>
function closeSidebar() {
    document.getElementById('sidebar').classList.add('-translate-x-full');
    document.getElementById('overlay').classList.add('opacity-0', 'pointer-events-none');
}

function openSidebar() {
    document.getElementById('sidebar').classList.remove('-translate-x-full');
    document.getElementById('overlay').classList.remove('opacity-0', 'pointer-events-none');
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && window.innerWidth < 768) closeSidebar();
});
</script>