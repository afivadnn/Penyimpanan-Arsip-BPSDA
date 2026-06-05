<?php
// models/Peminjaman.php

class Peminjaman {
    private $conn;
    private $table = "peminjaman";

    public function __construct($db) {
        $this->conn = $db;
    }

    // AMBIL SEMUA DATA (Join ke data_arsip)
    public function getAll($status = null, $keyword = null) {
        $query = "SELECT p.*, d.kode_jenis, d.nama_jenis, u.nama_lengkap as nama_petugas
                  FROM " . $this->table . " p
                  JOIN data_arsip d ON p.data_arsip_id = d.id
                  JOIN users u ON p.petugas_pinjam_id = u.id
                  WHERE 1=1";

        if ($status) {
            $query .= " AND p.status = :status";
        }

        if ($keyword) {
            $query .= " AND (d.kode_jenis LIKE :key OR d.nama_jenis LIKE :key OR p.nama_peminjam_luar LIKE :key)";
        }

        $query .= " ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($query);

        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        if ($keyword) {
            $k = "%{$keyword}%";
            $stmt->bindParam(':key', $k);
        }

        $stmt->execute();
        return $stmt;
    }

    // SIMPAN PEMINJAMAN BARU
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (data_arsip_id, nama_peminjam_luar, kontak_peminjam, tanggal_pinjam, tanggal_kembali_rencana, status, catatan_keperluan, petugas_pinjam_id)
                  VALUES (:arsip, :nama, :kontak, :tgl_pinjam, :tgl_balik, 'dipinjam', :catatan, :petugas)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':arsip', $data['data_arsip_id']);
        $stmt->bindParam(':nama', $data['nama_peminjam_luar']);
        $stmt->bindParam(':kontak', $data['kontak_peminjam']);
        $stmt->bindParam(':tgl_pinjam', $data['tanggal_pinjam']);
        $stmt->bindParam(':tgl_balik', $data['tanggal_kembali_rencana']);
        $stmt->bindParam(':catatan', $data['catatan_keperluan']);
        $stmt->bindParam(':petugas', $data['petugas_pinjam_id']);

        return $stmt->execute();
    }

    // PROSES PENGEMBALIAN
    public function kembalikan($id, $petugas_kembali_id) {
        $now = date('Y-m-d');
        $query = "UPDATE " . $this->table . " 
                  SET status = 'dikembalikan', 
                      tanggal_kembali_realisasi = :now, 
                      petugas_kembali_id = :petugas 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':now', $now);
        $stmt->bindParam(':petugas', $petugas_kembali_id);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Fungsi Get By ID (Untuk ambil data saat mau diedit)
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>