<?php
// controllers/AuthController.php

// Pastikan urutan include benar. database.php HARUS pertama karena memuat BASE_URL
require_once '../config/database.php';
require_once '../models/User.php';
require_once '../utils/functions.php';

// Mulai session jika belum ada (jaga-jaga)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = cleanInput($_POST['username']);
    $password = $_POST['password'];

    // Validasi Input Kosong
    if(empty($username) || empty($password)){
        setFlash('danger', 'Username dan Password wajib diisi!');
        header("Location: " . BASE_URL . "index.php");
        exit;
    }

    // Proses Login
    $loginData = $user->login($username, $password);

    if ($loginData === 'inactive') {
        setFlash('danger', 'Akun Anda dinonaktifkan. Hubungi Administrator.');
        header("Location: " . BASE_URL . "index.php");
        exit;
    }
    elseif ($loginData) {
        // --- LOGIN SUKSES: SET SESSION ---
        $_SESSION['user_id']      = $loginData['id'];
        $_SESSION['nama_lengkap'] = $loginData['nama_lengkap'];
        $_SESSION['role']         = $loginData['role'];
        $_SESSION['user_subag_id'] = $loginData['subag_id']; // Penting untuk fitur hak akses

        // --- PERBAIKAN UTAMA DI SINI ---
        // Gunakan BASE_URL untuk Redirect Absolut (Pasti Benar)
        header("Location: " . BASE_URL . "views/dashboard.php");
        exit;
        
    } else {
        // Login Gagal
        setFlash('danger', 'Username atau Password salah!');
        header("Location: " . BASE_URL . "index.php");
        exit;
    }
} else {
    // Jika akses langsung ke file ini tanpa POST
    header("Location: " . BASE_URL . "index.php");
    exit;
}
?>