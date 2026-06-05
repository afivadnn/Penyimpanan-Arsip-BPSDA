<?php
// models/Arsip.php

class Arsip {
    private $conn;
    private $table = "data_arsip"; // Nama tabel BARU

    public function __construct($db) {
        $this->conn = $db;
    }

    // --- FITUR UTAMA: GENERATE ID OTOMATIS (arsip-0001) ---
    public function generateNewId() {
        // 1. Ambil ID terakhir yang ada di database (Order by ID Descending)
        $query = "SELECT id FROM " . $this->table . " ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $lastData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($lastData) {
            // Jika ada data (misal: arsip-0015)
            $lastId = $lastData['id']; // "arsip-0015"
            
            // Ambil angkanya saja (pecah string berdasarkan strip "-")
            $parts = explode('-', $lastId); 
            $number = intval(end($parts)); // Ambil bagian akhir (0015 -> 15)
            
            // Tambah 1
            $newNumber = $number + 1;
        } else {
            // Jika tabel masih kosong, mulai dari 1
            $newNumber = 1;
        }

        // Format ulang menjadi arsip-XXXX (Padding 4 digit dengan nol)
        // Contoh: 1 -> arsip-0001, 15 -> arsip-0015, 100 -> arsip-0100
        return 'arsip-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }


    public function getAll($filters = []) {
        // PERBAIKAN: Tambahkan subquery untuk menghitung jumlah digital
        $query = "SELECT d.*, s.kode_subag, s.nama_subag, k.nama_kategori,
                  (SELECT COUNT(*) FROM berkas WHERE data_arsip_id = d.id) as jumlah_digital 
                  FROM " . $this->table . " d
                  JOIN subag s ON d.subag_id = s.id
                  JOIN kategori_arsip k ON d.kategori_id = k.id
                  WHERE 1=1";
        
    
        // Filter Subag
        if (!empty($filters['subag_id'])) {
            $query .= " AND d.subag_id = :subag_id";
        }
        // Filter Tahun
        if (!empty($filters['tahun'])) {
            $query .= " AND d.tahun = :tahun";
        }
        // Filter Keyword
        if (!empty($filters['keyword'])) {
            $query .= " AND (d.nama_jenis LIKE :keyword OR d.kode_jenis LIKE :keyword)";
        }
        // Filter Lokasi
        if (!empty($filters['lokasi'])) {
            $query .= " AND d.lokasi_simpan LIKE :lokasi";
        }

        $query .= " ORDER BY d.created_at DESC";

        $stmt = $this->conn->prepare($query);

        // Binding (Tetap sama)
        if (!empty($filters['subag_id'])) $stmt->bindParam(':subag_id', $filters['subag_id']);
        if (!empty($filters['tahun'])) $stmt->bindParam(':tahun', $filters['tahun']);
        if (!empty($filters['keyword'])) {
            $keyword = "%{$filters['keyword']}%";
            $stmt->bindParam(':keyword', $keyword);
        }
        if (!empty($filters['lokasi'])) {
            $lokasi = "%{$filters['lokasi']}%";
            $stmt->bindParam(':lokasi', $lokasi);
        }

        $stmt->execute();
        return $stmt;
    }

  // --- CREATE DATA BARU (PERBAIKAN) ---
    public function create($data) {
        // 1. Generate ID Otomatis jika tidak dikirim dari Controller
        if (empty($data['id'])) {
            $data['id'] = $this->generateNewId();
        }

        // 2. Query Insert (Saya ganti 'tingkat_perkembangan' jadi 'jumlah_fisik' agar sesuai fitur sebelumnya)
        $query = "INSERT INTO " . $this->table . " 
                  (id, subag_id, tahun, kategori_id, kode_jenis, nama_jenis, deskripsi, lokasi_simpan, jumlah_fisik)
                  VALUES (:id, :subag, :tahun, :kat, :kode, :nama, :desc, :lokasi, :jumlah)";
        
        $stmt = $this->conn->prepare($query);

        // 3. Binding Data
        $stmt->bindParam(":id", $data['id']);
        $stmt->bindParam(":subag", $data['subag_id']);
        $stmt->bindParam(":tahun", $data['tahun']);
        $stmt->bindParam(":kat", $data['kategori_id']);
        $stmt->bindParam(":kode", $data['kode_jenis']);
        $stmt->bindParam(":nama", $data['nama_jenis']);
        $stmt->bindParam(":desc", $data['deskripsi']);
        $stmt->bindParam(":lokasi", $data['lokasi_simpan']);
        
        // Handle jumlah fisik (Default 0 jika kosong)
        $jumlah = !empty($data['jumlah_fisik']) ? $data['jumlah_fisik'] : 0;
        $stmt->bindParam(":jumlah", $jumlah);

        try {
            if ($stmt->execute()) return "SUCCESS";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) return "DUPLICATE";
            return "ERROR: " . $e->getMessage();
        }
        return "ERROR";
    }

    // --- UPDATE DATA (PERBAIKAN) ---
    public function update($data) {
        // Hapus 'tingkat_perkembangan' karena kita tidak pakai itu di form
        $query = "UPDATE " . $this->table . " 
                  SET subag_id=:subag, tahun=:tahun, kategori_id=:kat, kode_jenis=:kode, 
                      nama_jenis=:nama, deskripsi=:desc, lokasi_simpan=:lokasi
                  WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":subag", $data['subag_id']);
        $stmt->bindParam(":tahun", $data['tahun']);
        $stmt->bindParam(":kat", $data['kategori_id']);
        $stmt->bindParam(":kode", $data['kode_jenis']);
        $stmt->bindParam(":nama", $data['nama_jenis']);
        $stmt->bindParam(":desc", $data['deskripsi']);
        $stmt->bindParam(":lokasi", $data['lokasi_simpan']);
        $stmt->bindParam(":id", $data['id']);

        try {
            if ($stmt->execute()) return "SUCCESS";
        } catch (PDOException $e) {
            return "ERROR: " . $e->getMessage();
        }
        return "ERROR";
    }

    public function updateJumlahFisik($id, $jumlah) {
        $query = "UPDATE " . $this->table . " SET jumlah_fisik = :jumlah WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':jumlah', $jumlah);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // --- GET BY ID ---
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id); // ID string
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- DELETE (Update: Hapus file & relasi) ---
    public function delete($id) {
        // Hapus data (Relasi ON DELETE CASCADE di database akan otomatis menghapus berkas & peminjaman)
        // Tapi file fisik di folder harus dihapus manual di Controller
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>