<?php
// File: utils/reset_admin.php
require_once '../config/database.php'; // Pastikan path ini benar sesuai struktur folder Anda

try {
    $db = (new Database())->getConnection();

    // 1. Data Baru
    $new_username = "BPSDA Sercit";
    $new_password = "seracita"; // Password asli
    $new_fullname = "Balai PSDA Serayu Citanduy";

    // 2. Enkripsi Password (WAJIB)
    // Menggunakan PASSWORD_DEFAULT agar kompatibel dengan password_verify() di sistem login
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // 3. Query Update
    // Kita asumsikan ID admin utama adalah 1. 
    // Jika ID admin Anda bukan 1, ganti angka 1 di bawah.
    $sql = "UPDATE users 
            SET username = :username, 
                password = :password, 
                nama_lengkap = :nama 
            WHERE id = 1"; 
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':username', $new_username);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':nama', $new_fullname);

    if($stmt->execute()) {
        echo "<h1>BERHASIL!</h1>";
        echo "Data admin (ID 1) telah diubah menjadi:<br>";
        echo "Username: <b>$new_username</b><br>";
        echo "Password: <b>$new_password</b> (Telah dienkripsi)<br>";
        echo "Nama: <b>$new_fullname</b><br><br>";
        echo "Silakan hapus file ini dan coba login.";
    } else {
        echo "Gagal mengupdate data.";
    }

} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}
?>