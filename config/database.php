<?php

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/arsip_psda/'); 
}

class Database {
    private $host = "localhost";
    private $db_name = "db_arsip_psda"; // Sesuaikan nama DB Tahap 1
    private $username = "root";         // Sesuaikan user DB
    private $password = "";             // Sesuaikan password DB
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                $this->username, 
                $this->password
            );
            // Mode error: Exception (PENTING untuk debugging)
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Karakter set: UTF-8
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Koneksi Database Gagal: " . $exception->getMessage();
            die(); // Stop script jika DB mati
        }
        return $this->conn;
    }
}
?>