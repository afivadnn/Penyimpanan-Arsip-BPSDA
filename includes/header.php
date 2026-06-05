<?php require_once __DIR__ . '/../utils/functions.php'; ?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= BASE_URL ?>assets/img/logo.png" type="image/x-icon">
    <title><?= $page_title ?? 'E-Arsip Digital' ?> • Balai PSDA Serayu Citanduy</title>

    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'] 
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
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
        /* Custom Scrollbar untuk Sidebar */
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 2px; }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.3); }

        /* Animasi Halaman */
        body { opacity: 0; animation: fadeIn 0.3s ease-in-out forwards; }
        @keyframes fadeIn { to { opacity: 1; } }
        
        .page-content { animation: slideUp 0.4s ease-out; }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Transisi Sidebar Mobile */
        #sidebar { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        #mobile-overlay { transition: opacity 0.3s ease-in-out; }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 antialiased font-sans">

    <div id="mobile-overlay" 
         onclick="closeSidebar()"
         class="fixed inset-0 bg-black/50 z-40 hidden opacity-0 transition-opacity backdrop-blur-sm md:hidden">
    </div>

    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="md:ml-72 min-h-screen flex flex-col transition-all duration-300">
        
        <script>
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');

            // Fungsi Buka Sidebar (Dipanggil dari Navbar)
            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                // Sedikit delay agar animasi opacity berjalan halus
                setTimeout(() => {
                    overlay.classList.remove('opacity-0');
                }, 10);
            }

            // Fungsi Tutup Sidebar (Dipanggil dari tombol X atau klik Overlay)
            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('opacity-0');
                
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 300); // Sesuaikan dengan durasi transition CSS
            }
        </script>