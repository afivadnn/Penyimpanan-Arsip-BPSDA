<?php
// controllers/PeminjamanController.php
require_once '../config/database.php';
require_once '../models/Peminjaman.php';
require_once '../utils/functions.php';

checkLogin();

$db = (new Database())->getConnection();
$pinjamModel = new Peminjaman($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    // Validasi Input Umum
    if ($action == 'store' || $action == 'update') {
        if (empty($_POST['data_arsip_id']) || empty($_POST['nama_peminjam'])) {
            setFlash('danger', 'Data arsip dan nama peminjam wajib diisi!');
            redirect('views/peminjaman/form.php');
        }

        $data = [
            'data_arsip_id'         => $_POST['data_arsip_id'],
            'nama_peminjam_luar'    => cleanInput($_POST['nama_peminjam']),
            'kontak_peminjam'       => cleanInput($_POST['kontak']),
            'tanggal_pinjam'        => $_POST['tanggal_pinjam'],
            'tanggal_kembali_rencana'=> $_POST['tanggal_kembali'],
            'catatan_keperluan'     => cleanInput($_POST['keperluan']),
            'petugas_pinjam_id'     => $_SESSION['user_id']
        ];
    }

    // --- 1. SIMPAN BARU ---
    if ($action == 'store') {
        if ($pinjamModel->create($data)) {
            setFlash('success', 'Peminjaman berhasil dicatat.');
            redirect('views/peminjaman/index.php');
        } else {
            setFlash('danger', 'Gagal menyimpan data.');
            redirect('views/peminjaman/form.php');
        }
    }
    
}

// --- 4. HAPUS DATA (GET) ---
elseif (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    $pinjam = $pinjamModel->getById($id);
    
    if ($pinjam) {
        if ($pinjamModel->delete($id)) {
            setFlash('success', 'Data peminjaman berhasil dihapus.');
        } else {
            setFlash('danger', 'Gagal menghapus data.');
        }
    }
    redirect('views/peminjaman/index.php');
}
?>