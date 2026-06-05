<?php
// controllers/UserController.php
require_once '../config/database.php';
require_once '../utils/functions.php';

checkLogin();
// Proteksi: Hanya Super Admin (subag_id NULL) yang boleh akses file ini
if (!empty($_SESSION['user_subag_id'])) {
    setFlash('danger', 'Akses ditolak. Anda bukan Super Admin.');
    redirect('views/dashboard.php');
}

$db = (new Database())->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {

    // --- 1. TAMBAH USER BARU ---
    if ($_POST['action'] == 'store') {
        $username = cleanInput($_POST['username']);
        $password = $_POST['password'];
        $nama     = cleanInput($_POST['nama_lengkap']);
        $role     = $_POST['role']; // 'admin' atau 'petugas'
        
        // Logika Subag: Jika admin, subag NULL. Jika petugas, wajib isi subag.
        $subag_id = ($role === 'admin') ? NULL : $_POST['subag_id'];

        // Validasi
        if (empty($username) || empty($password) || empty($nama)) {
            setFlash('danger', 'Semua data wajib diisi!');
            redirect('views/users/input.php');
        }

        // Cek Username Kembar
        $cek = $db->prepare("SELECT id FROM users WHERE username = ?");
        $cek->execute([$username]);
        if ($cek->rowCount() > 0) {
            setFlash('danger', 'Username sudah digunakan, cari yang lain.');
            redirect('views/users/input.php');
        }

        // Hash Password
        $passHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password, nama_lengkap, role, subag_id, status_aktif) 
                VALUES (:user, :pass, :nama, :role, :subag, 1)";
        $stmt = $db->prepare($sql);
        
        try {
            $stmt->execute([
                ':user' => $username,
                ':pass' => $passHash,
                ':nama' => $nama,
                ':role' => 'admin', // Di DB kita set role 'admin' semua agar bisa login, pembeda hak akses ada di subag_id
                ':subag'=> $subag_id
            ]);
            setFlash('success', 'User berhasil ditambahkan.');
            redirect('views/users/index.php');
        } catch (PDOException $e) {
            setFlash('danger', 'Gagal menyimpan: ' . $e->getMessage());
            redirect('views/users/input.php');
        }
    }

    // --- B. UPDATE DATA USER (Edit Username/Nama/Subag) ---
    elseif ($_POST['action'] == 'update') {
        $id       = $_POST['id'];
        $nama     = cleanInput($_POST['nama_lengkap']);
        $username = cleanInput($_POST['username']);
        $role     = $_POST['role']; // 'admin' atau 'petugas'
        
        // Logika Subag: Jika admin, subag NULL. Jika petugas, ambil dari dropdown
        $subag_id = ($role === 'admin') ? NULL : $_POST['subag_id'];

        // 1. Validasi Input Kosong
        if (empty($nama) || empty($username)) {
            setFlash('danger', 'Nama dan Username wajib diisi.');
            redirect('views/users/index.php');
        }

        // 2. Cek Username Kembar (Kecuali punya diri sendiri)
        // Logic: Cari user lain yang usernamenya sama, TAPI id-nya bukan id yang sedang diedit
        $cek = $db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $cek->execute([$username, $id]);
        
        if ($cek->rowCount() > 0) {
            setFlash('danger', 'Username sudah dipakai user lain. Pilih yang unik.');
            redirect('views/users/index.php');
        }

        // 3. Proses Update
        $sql = "UPDATE users SET username = :user, nama_lengkap = :nama, subag_id = :subag WHERE id = :id";
        $stmt = $db->prepare($sql);
        
        try {
            $stmt->execute([
                ':user'  => $username,
                ':nama'  => $nama,
                ':subag' => $subag_id,
                ':id'    => $id
            ]);
            setFlash('success', 'Data user berhasil diperbarui.');
        } catch (PDOException $e) {
            setFlash('danger', 'Gagal update: ' . $e->getMessage());
        }
        redirect('views/users/index.php');
    }

    elseif ($_POST['action'] == 'reset_password') {
        $id = $_POST['id'];
        $new_password = $_POST['new_password'];

        if (empty($id) || empty($new_password)) {
            setFlash('danger', 'Password baru tidak boleh kosong!');
        } else {
            // Hash password baru
            $passHash = password_hash($new_password, PASSWORD_DEFAULT);
            
            $stmt = $db->prepare("UPDATE users SET password = :pass WHERE id = :id");
            if ($stmt->execute([':pass' => $passHash, ':id' => $id])) {
                setFlash('success', 'Password berhasil di-reset.');
            } else {
                setFlash('danger', 'Gagal mereset password.');
            }
        }
        redirect('views/users/index.php');
    }

    // --- 2. HAPUS USER ---
    elseif ($_POST['action'] == 'delete') {
        $id = $_POST['id'];
        // Jangan biarkan hapus diri sendiri
        if ($id == $_SESSION['user_id']) {
            setFlash('danger', 'Tidak bisa menghapus akun sendiri!');
        } else {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            setFlash('success', 'User berhasil dihapus.');
        }
        redirect('views/users/index.php');
    }
}
?>