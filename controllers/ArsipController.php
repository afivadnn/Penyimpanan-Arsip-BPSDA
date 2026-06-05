<?php
// controllers/ArsipController.php
require_once '../config/database.php';
require_once '../models/Arsip.php';
require_once '../utils/functions.php';

checkLogin();

$db = (new Database())->getConnection();
$arsip = new Arsip($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    // --- A. PROSES SIMPAN (INPUT BARU) ---
    if ($action == 'store') {
        // LOGIKA KEAMANAN SUBAG
        $subag_target = $_POST['subag_id'];
        if (!empty($_SESSION['user_subag_id'])) {
            $subag_target = $_SESSION['user_subag_id']; 
        }

        $data = [
            'subag_id'      => $subag_target,
            'tahun'         => $_POST['tahun'],
            'kategori_id'   => $_POST['kategori_id'],
            'kode_jenis'    => strtoupper($_POST['kode_jenis']),
            'nama_jenis'    => $_POST['nama_jenis'],
            'lokasi_simpan' => $_POST['lokasi_simpan'],
            'deskripsi'     => $_POST['deskripsi'],
            'jumlah_fisik'  => 0 
        ];

        if ($arsip->create($data)) {
            setFlash('success', 'Data arsip berhasil disimpan.');
            
            // --- PERBAIKAN REDIRECT (Gunakan BASE_URL) ---
            if (!empty($_SESSION['user_subag_id'])) {
                 header("Location: " . BASE_URL . "views/arsip/index.php");
            } else {
                 header("Location: " . BASE_URL . "views/arsip/index.php");
            }
            exit;

        } else {
            setFlash('danger', 'Gagal menyimpan data.');
            header("Location: " . BASE_URL . "views/arsip/input.php");
            exit;
        }
    }

    // --- B. PROSES UPDATE (EDIT DATA UTAMA) ---
    elseif ($action == 'update') {
        $id = $_POST['id'];

        // KEAMANAN EDIT
        if (!empty($_SESSION['user_subag_id'])) {
            $existingData = $arsip->getById($id);
            if ($existingData['subag_id'] != $_SESSION['user_subag_id']) {
                setFlash('danger', 'KEAMANAN: Anda tidak berhak mengedit arsip Bagian lain!');
                header("Location: " . BASE_URL . "views/arsip/index.php");
                exit;
            }
        }

        $data = [
            'id'            => $id,
            'kode_jenis'    => strtoupper($_POST['kode_jenis']),
            'subag_id'      => $_POST['subag_id'],
            'tahun'         => $_POST['tahun'],
            'nama_jenis'    => $_POST['nama_jenis'],
            'kategori_id'   => $_POST['kategori_id'],
            'lokasi_simpan' => $_POST['lokasi_simpan'],
            'deskripsi'     => $_POST['deskripsi']
        ];

        if ($arsip->update($data)) {
            setFlash('success', 'Data arsip berhasil diperbarui.');
            // --- PERBAIKAN REDIRECT ---
            header("Location: " . BASE_URL . "views/arsip/detail.php?id=" . $id);
            exit;
        } else {
            setFlash('danger', 'Gagal memperbarui data.');
            header("Location: " . BASE_URL . "views/arsip/edit.php?id=" . $id);
            exit;
        }
    }

    // --- C. UPDATE JUMLAH FISIK (YANG TADI ERROR) ---
    elseif ($action == 'update_fisik') {
        $id = $_POST['id'];
        
        // KEAMANAN
        if (!empty($_SESSION['user_subag_id'])) {
            $existingData = $arsip->getById($id);
            if ($existingData['subag_id'] != $_SESSION['user_subag_id']) {
                setFlash('danger', 'Akses Ditolak.');
                header("Location: " . BASE_URL . "views/arsip/index.php");
                exit;
            }
        }

        $jumlah = intval($_POST['jumlah_fisik']);
        
        if ($arsip->updateJumlahFisik($id, $jumlah)) {
            setFlash('success', "Jumlah berkas fisik berhasil diubah menjadi: $jumlah");
        } else {
            setFlash('danger', 'Gagal mengubah jumlah fisik.');
        }

        // --- PERBAIKAN REDIRECT UTAMA ---
        // Menggunakan BASE_URL agar linknya menjadi http://localhost/arsip_psda/views/...
        header("Location: " . BASE_URL . "views/arsip/detail.php?id=" . $id);
        exit;
    }
}

// --- D. PROSES DELETE (HAPUS) ---
elseif (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];

    // KEAMANAN DELETE
    if (!empty($_SESSION['user_subag_id'])) {
        $existingData = $arsip->getById($id);
        if (!$existingData || $existingData['subag_id'] != $_SESSION['user_subag_id']) {
            setFlash('danger', 'KEAMANAN: Anda mencoba menghapus data milik Bagian lain!');
            header("Location: " . BASE_URL . "views/arsip/index.php");
            exit;
        }
    }

    if ($arsip->delete($id)) {
        setFlash('success', 'Data arsip berhasil dihapus.');
    } else {
        setFlash('danger', 'Gagal menghapus data.');
    }

    header("Location: " . BASE_URL . "views/arsip/index.php");
    exit;
}
?>