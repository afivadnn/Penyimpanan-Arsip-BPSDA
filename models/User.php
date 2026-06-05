<?php
// models/User.php

class User {
    private $conn;
    private $table = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Fungsi Login
    public function login($username, $password) {
        // PERBAIKAN DI SINI:
        // Gunakan SELECT * atau panggil kolom 'password', JANGAN 'password_hash'
        $query = "SELECT * FROM " . $this->table . " WHERE username = :username LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Cek status aktif (Opsional, jika fitur status aktif sudah jalan)
            if (isset($row['status_aktif']) && $row['status_aktif'] == 0) {
                return 'inactive'; // User dinonaktifkan
            }

            // Verifikasi Password (Hash vs Plaintext)
            // Pastikan kolom database namanya 'password'
            if (password_verify($password, $row['password'])) {
                return $row; // Login Sukses, kembalikan data user
            }
        }
        
        return false; // Login Gagal
    }

    // Fungsi Ambil User by ID
    public function getUserById($id) {
        $query = "SELECT id, username, nama_lengkap, role, subag_id FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>