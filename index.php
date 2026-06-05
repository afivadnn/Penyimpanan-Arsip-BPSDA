<?php
require_once 'utils/functions.php';
if (isset($_SESSION['user_id'])) header("Location: " . BASE_URL . "views/dashboard.php");
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= BASE_URL ?>assets/img/logo.png" type="image/x-icon">
    <title>Login • E-Arsip PSDA Serayu Citanduy</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%);
        }
        .input-focus-ring:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }
        /* Hide reveal button in Edge */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }
    </style>
</head>
<body class="h-full bg-gradient-to-br from-blue-50 via-white to-indigo-50 flex items-center justify-center px-4 py-12">

    <div class="w-full max-w-md">
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl overflow-hidden border border-white/20">
            
            <div class="bg-gradient-primary px-8 py-12 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="relative z-10">
                    <div class="w-24 h-24 mx-auto mb-6 bg-white/20 backdrop-blur-sm rounded-full p-3 flex items-center justify-center shadow-xl">
                        <img src="<?= BASE_URL ?>assets/img/logo.png" alt="Logo" class="w-20 h-20 rounded-full object-contain drop-shadow-lg">
                    </div>
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">E-ARSIP DIGITAL</h1>
                    <p class="text-blue-100 text-lg font-medium mt-2">Balai PSDA Serayu Citanduy</p>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-2 bg-white/10"></div>
            </div>

            <div class="px-8 pt-8 pb-10">
                
                <?php if (isset($_SESSION['flash'])): ?>
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2 animate-pulse">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"/></svg>
                        <?= $_SESSION['flash']['message'] ?>
                    </div>
                    <?php unset($_SESSION['flash']); ?>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>controllers/AuthController.php" method="POST" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input 
                                type="text" 
                                name="username" 
                                id="username"
                                class="w-full pl-12 pr-4 py-4 bg-gray-50/70 border border-gray-300 rounded-xl focus:border-primary-600 focus:bg-white input-focus-ring transition-all duration-200 text-gray-900 placeholder-gray-400 font-medium"
                                placeholder="Masukkan username Anda"
                                required 
                                autofocus>
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.104-.896-2-2-2s-2 .896-2 2 2 4 2 4m2-4c0-1.104.896-2 2-2s2 .896 2 2-2 4-2 4m-2-4v6"/></svg>
                            </div>
                            
                            <input 
                                type="password" 
                                name="password" 
                                id="password"
                                class="w-full pl-12 pr-12 py-4 bg-gray-50/70 border border-gray-300 rounded-xl focus:border-primary-600 focus:bg-white input-focus-ring transition-all duration-200 text-gray-900 placeholder-gray-400 font-medium"
                                placeholder="Masukkan password"
                                required>

                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-primary-600 transition-colors focus:outline-none">
                                <svg id="eye-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eye-off-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button 
                        type="submit"
                        class="w-full bg-gradient-to-r from-primary-800 to-primary-900 hover:from-primary-900 hover:to-blue-950 text-white font-bold text-lg py-4 rounded-xl shadow-xl transform transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl active:translate-y-0">
                        <span class="flex items-center justify-center gap-3">
                            <span>MASUK KE SISTEM</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </span>
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-xs text-gray-500">
                        Sistem ini dikelola oleh<br>
                        <span class="font-semibold text-gray-700">Balai PSDA Serayu Citanduy</span>
                    </p>
                </div>
            </div>

            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-5 text-center border-t border-gray-200">
                <p class="text-xs text-gray-600 font-medium">
                    © <?= date('Y') ?> Dinas PUSDATARU Provinsi Jawa Tengah • All rights reserved
                </p>
            </div>
        </div>

        <div class="mt-8 text-center">
            <p class="text-xs text-gray-500">Versi 2.0 • Terakhir diperbarui Desember 2025</p>
        </div>
    </div>

    <script>
        // Toggle Password Logic
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            }
        }

        // Auto-hide flash message after 5 seconds
        const flash = document.querySelector('[class*="bg-red-50"]');
        if (flash) {
            setTimeout(() => {
                flash.style.transition = 'all 0.6s ease';
                flash.style.opacity = '0';
                flash.style.transform = 'translateY(-10px)';
                setTimeout(() => flash.remove(), 600);
            }, 5000);
        }
    </script>
</body>
</html>