<?php
// utils/functions.php

session_start(); // Mulai session di setiap halaman yang include file ini

// 1. Base URL (Ganti sesuai nama folder project Anda)
define('BASE_URL', 'http://localhost/arsip_psda/');

// 2. Fungsi Redirect Aman
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit;
}

// 3. Fungsi Sanitasi Input (Cegah XSS)
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// 4. Cek Login (Middleware Sederhana)
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        redirect('index.php');
    }
}

// ... kode sebelumnya ...

// FUNGSI BARU: Format Tanggal Indonesia Lengkap (Hari, dd MMMM yyyy)
function format_tanggal_indo($date_string = null) {
    // Jika kosong, pakai tanggal hari ini
    if ($date_string == null) {
        $date_string = date('Y-m-d');
    }

    $timestamp = strtotime($date_string);

    // Array Hari
    $hari_array = [
        'Sunday'    => 'Minggu',
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu'
    ];

    // Array Bulan
    $bulan_array = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];

    $hari_en = date('l', $timestamp);
    $hari_id = $hari_array[$hari_en];
    
    $tgl = date('d', $timestamp);
    $bln = (int)date('m', $timestamp); // Di-cast ke int agar nol di depan hilang (01 -> 1)
    $thn = date('Y', $timestamp);

    return "$hari_id, $tgl " . $bulan_array[$bln] . " $thn";
}

// 5. Cek Role Admin
function checkAdmin() {
    if ($_SESSION['role'] !== 'admin') {
        echo "Akses Ditolak! Anda bukan Admin.";
        exit;
    }
}

// 6. Flash Message (Pesan Sukses/Gagal sekali muncul)
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // success, danger, warning
        'message' => $message
    ];
}


?>