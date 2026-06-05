<?php
// controllers/BerkasController.php
require_once '../config/database.php';
require_once '../models/Berkas.php';
require_once '../utils/functions.php';

checkLogin();

$db = (new Database())->getConnection();
$berkasModel = new Berkas($db);

// --- PROSES UPLOAD ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'upload') {

    $data_arsip_id = $_POST['data_arsip_id']; 
    
    $redirect_url = "../views/arsip/detail.php?id=" . $data_arsip_id;

    // 1. Cek apakah file ada
    if (!isset($_FILES['file_pdf']) || $_FILES['file_pdf']['error'] != 0) {
        setFlash('danger', 'Error: Pilih file PDF yang valid!');
        redirect($redirect_url);
    }

    $file = $_FILES['file_pdf'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // 2. Validasi Ekstensi & MIME Type
    $allowed_types = ['application/pdf'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if ($ext != 'pdf' || !in_array($mime, $allowed_types)) {
        setFlash('danger', 'Hanya file PDF yang diperbolehkan!');
        redirect($redirect_url);
    }

    // 3. Validasi Ukuran (Max 10MB)
    if ($file['size'] > 10 * 1024 * 1024) {
        setFlash('danger', 'Ukuran file terlalu besar! Maksimal 10MB.');
        redirect($redirect_url);
    }

    // 4. Proses Upload & Rename
    $new_filename = 'arsip_' . uniqid() . '_' . time() . '.pdf';
    $upload_dir = '../uploads/arsip/';
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename)) {
        
        // [PERBAIKAN 2] Panggil fungsi getNextNomor pakai ID baru
        $nomor_urut = $berkasModel->getNextNomor($data_arsip_id);
        
        $data = [
            'data_arsip_id' => $data_arsip_id, // Kolom Database Baru
            'nomor_berkas' => $nomor_urut,
            'nama_berkas_asli' => $file['name'],
            'nama_file_tersimpan' => $new_filename,
            'ukuran_file_kb' => round($file['size'] / 1024),
            'uploaded_by' => $_SESSION['user_id']
        ];

        if ($berkasModel->create($data)) {
            setFlash('success', 'Berkas PDF berhasil diupload!');
        } else {
            unlink($upload_dir . $new_filename);
            setFlash('danger', 'Gagal menyimpan data ke database.');
        }
    } else {
        setFlash('danger', 'Gagal memindahkan file ke server.');
    }

    header("Location: " . BASE_URL . "views/arsip/detail.php?id=" . $data_arsip_id);
    exit;
}

// --- PROSES HAPUS BERKAS ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $berkas_id = $_GET['id'];
    $data = $berkasModel->getById($berkas_id);

    if ($data) {
        // [PERBAIKAN 3] Ambil ID induk untuk redirect
        $data_arsip_id = $data['data_arsip_id']; 
        
        $filepath = '../uploads/arsip/' . $data['nama_file_tersimpan'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }

        $berkasModel->delete($berkas_id);
        
        setFlash('success', 'Berkas berhasil dihapus.');
        header("Location: " . BASE_URL . "views/arsip/detail.php?id=" . $data_arsip_id);
        exit;
    }
}
?>