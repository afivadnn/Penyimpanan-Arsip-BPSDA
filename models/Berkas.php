<?php
// models/Berkas.php

class Berkas {
    private $conn;
    private $table = "berkas";

    public function __construct($db) {
        $this->conn = $db;
    }

    // [PERBAIKAN] Ganti jenis_arsip_id jadi data_arsip_id
    public function getNextNomor($data_arsip_id) {
        $query = "SELECT MAX(nomor_berkas) as max_nomor FROM " . $this->table . " WHERE data_arsip_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $data_arsip_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['max_nomor'] + 1;
    }

    // [PERBAIKAN] Insert ke kolom data_arsip_id
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (data_arsip_id, nomor_berkas, nama_berkas_asli, nama_file_tersimpan, ukuran_file_kb, uploaded_by)
                  VALUES (:id_arsip, :nomor, :nama_asli, :nama_simpan, :ukuran, :user)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_arsip', $data['data_arsip_id']);
        $stmt->bindParam(':nomor', $data['nomor_berkas']);
        $stmt->bindParam(':nama_asli', $data['nama_berkas_asli']);
        $stmt->bindParam(':nama_simpan', $data['nama_file_tersimpan']);
        $stmt->bindParam(':ukuran', $data['ukuran_file_kb']);
        $stmt->bindParam(':user', $data['uploaded_by']);

        return $stmt->execute();
    }

    // [PERBAIKAN] Select where data_arsip_id
    public function getByArsipId($data_arsip_id) {
        $query = "SELECT b.*, u.nama_lengkap as uploader 
                  FROM " . $this->table . " b
                  JOIN users u ON b.uploaded_by = u.id
                  WHERE b.data_arsip_id = :id 
                  ORDER BY b.uploaded_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $data_arsip_id);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>