<header class="bg-white/90 backdrop-blur-md border-b border-gray-200 sticky top-0 z-30 transition-all duration-300">
    <div class="px-4 sm:px-6 py-3 flex items-center justify-between">
        
        <div class="flex items-center gap-3 md:gap-4 overflow-hidden">
            
            <button type="button" 
                    onclick="openSidebar()"
                    class="md:hidden p-2 -ml-2 rounded-xl text-gray-600 hover:bg-gray-100 hover:text-blue-600 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-100">
                <i class="fas fa-bars text-xl"></i>
            </button>

            <div class="min-w-0"> <h1 class="text-lg md:text-2xl font-bold text-gray-800 truncate leading-tight">
                    <?= $page_title ?? 'Dashboard' ?>
                </h1>
                <?php if (isset($page_subtitle) && $page_subtitle != ''): ?>
                    <p class="text-xs md:text-sm text-gray-500 truncate hidden sm:block">
                        <?= $page_subtitle ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex items-center gap-4 flex-shrink-0">
            
            <div class="hidden md:block text-right">
                <p class="text-sm font-bold text-gray-700 leading-none">
                    <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>
                </p>
                <p class="text-[11px] text-gray-500 font-medium uppercase tracking-wide mt-1">
                    <?= htmlspecialchars($_SESSION['role'] == 'admin' ? 'Super Admin' : 'Petugas') ?>
                </p>
            </div>

            <div class="relative" id="user-menu-container">
                <button onclick="toggleUserMenu()" 
                        class="w-10 h-10 md:w-11 md:h-11 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 
                               flex items-center justify-center text-white font-bold text-lg 
                               shadow-md shadow-blue-200 hover:shadow-lg hover:shadow-blue-300 
                               ring-2 ring-white transition-all transform hover:scale-105 active:scale-95">
                    <?= strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)) ?>
                </button>

                <div id="user-dropdown" 
                     class="hidden absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 transform origin-top-right transition-all duration-200 z-50">
                    
                    <div class="px-4 py-3 border-b border-gray-100 md:hidden bg-gray-50/50 rounded-t-2xl">
                        <p class="text-sm font-bold text-gray-800 truncate">
                            <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>
                        </p>
                        <p class="text-xs text-gray-500 font-medium uppercase">
                            <?= htmlspecialchars($_SESSION['role']) ?>
                        </p>
                    </div>

                    <div class="p-2">
                        <?php if (empty($_SESSION['user_subag_id'])): ?>
                            <a href="<?= BASE_URL ?>views/users/index.php" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-700 rounded-xl transition-colors">
                                <i class="fas fa-users-cog w-5 text-center"></i>
                                Manajemen User
                            </a>
                        <?php endif; ?>
                        
                        <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-700 rounded-xl transition-colors">
                            <i class="fas fa-user-circle w-5 text-center"></i>
                            Profil Saya
                        </a>
                        
                        <div class="h-px bg-gray-100 my-1"></div>
                        
                        <a href="<?= BASE_URL ?>logout.php" 
                           onclick="return confirm('Yakin ingin keluar?')"
                           class="flex items-center gap-3 px-3 py-2.5 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 rounded-xl transition-colors font-medium">
                            <i class="fas fa-sign-out-alt w-5 text-center"></i>
                            Keluar
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>

<script>
    function toggleUserMenu() {
        const dropdown = document.getElementById('user-dropdown');
        dropdown.classList.toggle('hidden');
    }

    // Tutup dropdown jika klik di luar area
    document.addEventListener('click', function(event) {
        const container = document.getElementById('user-menu-container');
        const dropdown = document.getElementById('user-dropdown');
        
        if (!container.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>