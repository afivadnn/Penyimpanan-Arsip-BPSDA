<?php
// controllers/export_arsip.php

require_once '../config/database.php';
require_once '../models/Arsip.php';
require_once '../utils/functions.php';

checkLogin();

// 1. Tangkap Filter URL
$keyword = $_GET['q'] ?? '';
$filter_tahun = $_GET['tahun'] ?? '';
$filter_subag = $_GET['subag'] ?? '';
$filter_lokasi = $_GET['lokasi'] ?? '';

// 2. Ambil Data
$db = (new Database())->getConnection();
$arsipModel = new Arsip($db);

$data = $arsipModel->getAll([
    'keyword'  => $keyword,
    'tahun'    => $filter_tahun,
    'subag_id' => $filter_subag,
    'lokasi'   => $filter_lokasi
]);

// 3. Header Excel
$filename = "Rekap_Arsip_" . date('Y-m-d_His') . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache"); 
header("Expires: 0");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .table { border-collapse: collapse; width: 100%; }
        .table th, .table td { border: 1px solid #000; padding: 5px; vertical-align: top; }
        .header { background-color: #4f81bd; color: #fff; text-align: center; font-weight: bold; }
        .title { font-size: 16px; font-weight: bold; text-align: center; margin-bottom: 5px; }
        .subtitle { font-size: 12px; text-align: center; margin-bottom: 20px; }
        .text-center { text-align: center; }
        .str { mso-number-format:"\@"; } /* Format Text agar nol di depan tidak hilang */
    </style>
</head>
<body>

    <div class="title">PEMERINTAH PROVINSI JAWA TENGAH</div>
    <div class="title">DINAS PUSDATARU - BALAI PSDA SERAYU CITANDUY</div>
    <div class="subtitle">LAPORAN DATA ARSIP DIGITAL & FISIK</div>
    <br>

    <table class="table">
        <thead>
            <tr class="header">
                <th width="5%">No</th>
                <th width="15%">Kode Arsip</th>
                <th width="10%">Bagian</th>
                <th width="8%">Tahun</th>
                <th width="15%">Kategori</th>
                <th width="25%">Judul / Uraian</th>
                <th width="10%">Lokasi Fisik</th>
                <th width="8%">Jml Fisik</th>
                <th width="8%">Jml Digital</th>
                <th width="15%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if ($data->rowCount() > 0):
                while($row = $data->fetch(PDO::FETCH_ASSOC)): 
            ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td class="str"><b><?= $row['kode_jenis'] ?></b></td>
                <td class="text-center"><?= $row['kode_subag'] ?></td>
                <td class="text-center"><?= $row['tahun'] ?></td>
                <td><?= $row['nama_kategori'] ?></td>
                <td><?= $row['nama_jenis'] ?></td>
                <td><?= $row['lokasi_simpan'] ?: '-' ?></td>
                
                <td class="text-center"><?= $row['jumlah_fisik'] ?></td>
                <td class="text-center"><?= $row['jumlah_digital'] ?></td>
                
                <td><?= $row['deskripsi'] ?: '-' ?></td>
            </tr>
            <?php 
                endwhile;
            else:
            ?>
            <tr>
                <td colspan="10" class="text-center" style="padding: 20px;">Data tidak ditemukan.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <br>
    <table>
        <tr>
            <td colspan="8"></td>
            <td colspan="2" class="text-center">
                Dicetak pada: <?= date('d-m-Y H:i') ?><br>
                Petugas,<br><br><br><br>
                <b><?= $_SESSION['nama_lengkap'] ?></b>
            </td>
        </tr>
    </table>

</body>
</html>                     