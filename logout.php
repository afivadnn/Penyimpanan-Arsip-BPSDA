<?php
// logout.php
// Pastikan tidak ada output HTML sebelum tag PHP ini!

require_once 'utils/functions.php';

// 1. Mulai session (jika belum dimulai)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Kosongkan semua variabel session
$_SESSION = [];

// 3. Hancurkan cookie session (Pembersihan Total)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Hancurkan session di server
session_destroy();

// 5. Mulai session BARU untuk pesan Flash (Setelah destroy, start lagi untuk user baru/tamu)
session_start();
$_SESSION['flash'] = [
    'type' => 'success',
    'message' => 'Anda berhasil logout.'
];

// 6. REDIRECT ABSOLUT (Menggunakan BASE_URL)
// Pastikan BASE_URL sudah benar di config/database.php
header("Location: " . BASE_URL . "index.php");
exit;
?>