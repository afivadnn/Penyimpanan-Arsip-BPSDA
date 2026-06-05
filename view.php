<?php
// view.php
require_once 'utils/functions.php';
require_once 'config/database.php';

// Cek Login (Opsional, aktifkan jika file bersifat rahasia)
// checkLogin(); 

if (isset($_GET['f'])) {
    $file = basename($_GET['f']); // Ambil nama file & bersihkan dari karakter aneh
    $filepath = 'uploads/arsip/' . $file;

    // Cek apakah file benar-benar ada
    if (file_exists($filepath)) {
        // Tentukan tipe konten sebagai PDF
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $file . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        
        // Bersihkan output buffer agar file tidak rusak
        ob_clean();
        flush();
        
        // Baca file dan kirim ke browser
        readfile($filepath);
        exit;
    } else {
        echo "<h1>404</h1><p>File tidak ditemukan di server.</p>";
        echo "<p>Path: $filepath</p>"; // Debugging
    }
} else {
    echo "<h1>Error</h1><p>Parameter file tidak valid.</p>";
}
?>