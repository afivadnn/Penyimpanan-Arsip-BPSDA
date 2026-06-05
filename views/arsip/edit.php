<?php
// views/arsip/edit.php
require_once '../../utils/functions.php';
require_once '../../config/database.php';
require_once '../../models/Arsip.php';

checkLogin();

if (!isset($_GET['id'])) redirect('views/arsip/index.php');
$id = $_GET['id'];

$db = (new Database())->getConnection();
$arsipModel = new Arsip($db);

// Ambil data arsip
$data = $arsipModel->getById($id);

if (!$data) {
    setFlash('danger', 'Data arsip tidak ditemukan.');
    redirect('views/arsip/index.php');
}

// AMBIL DATA MASTER
$subags = $db->query("SELECT * FROM subag ORDER BY kode_subag ASC")->fetchAll(PDO::FETCH_ASSOC);
$kategoris = $db->query("SELECT * FROM kategori_arsip")->fetchAll(PDO::FETCH_ASSOC);
$lokasis = $db->query("SELECT DISTINCT lokasi_simpan FROM data_arsip WHERE lokasi_simpan != '' AND lokasi_simpan IS NOT NULL ORDER BY lokasi_simpan ASC")->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Edit Arsip";
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';
?>

<div class="max-w-5xl mx-auto p-6 pb-20">
    <a href="index.php" class="inline-flex items-center gap-2 text-gray-500 hover:text-amber-700 font-medium mb-6 transition group">
        <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i> Kembali ke Daftar
    </a>

    <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-3xl shadow-xl p-8 text-white mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
        
        <div class="relative z-10 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-extrabold mb-2">Edit Map Arsip</h1>
                <p class="text-orange-100">Anda sedang mengubah data arsip:</p>
                <div class="mt-4 flex items-center gap-3">
                    <span class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-xl border border-white/30 font-mono text-xl font-bold">
                        <?= htmlspecialchars($data['kode_jenis']) ?>
                    </span>
                </div>
            </div>
            <div class="hidden md:block opacity-20 rotate-12">
                <i class="fas fa-edit text-9xl"></i>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="mb-8 bg-red-50 border-l-4 border-red-500 text-red-700 p-5 rounded-r-xl flex items-center gap-3 animate-pulse">
            <i class="fas fa-exclamation-triangle text-2xl"></i>
            <div><?= $_SESSION['flash']['message'] ?></div>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>controllers/ArsipController.php" method="POST" class="bg-white rounded-3xl shadow-lg border border-gray-200 overflow-hidden">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?= $data['id'] ?>">
        
        <input type="hidden" name="subag_id" value="<?= $data['subag_id'] ?>">
        <input type="hidden" name="tahun" value="<?= $data['tahun'] ?>">

        <div class="p-8 lg:p-12 space-y-10">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-gray-50 p-6 rounded-2xl border border-gray-200">
                <div>
                    <label class="block text-gray-500 font-bold mb-2 text-sm uppercase tracking-wide">Sub Bagian</label>
                    <div class="text-gray-800 font-bold text-lg flex items-center gap-2">
                        <i class="fas fa-building text-gray-400"></i>
                        <?php 
                            // Cari nama subag
                            $key = array_search($data['subag_id'], array_column($subags, 'id'));
                            echo $key !== false ? htmlspecialchars($subags[$key]['nama_subag']) : '-';
                        ?>
                        <span class="text-xs text-gray-400 font-normal ml-2">(Tidak dapat diubah)</span>
                    </div>
                </div>
                <div>
                    <label class="block text-gray-500 font-bold mb-2 text-sm uppercase tracking-wide">Tahun Arsip</label>
                    <div class="text-gray-800 font-bold text-lg flex items-center gap-2">
                        <i class="fas fa-calendar text-gray-400"></i>
                        <?= htmlspecialchars($data['tahun']) ?>
                        <span class="text-xs text-gray-400 font-normal ml-2">(Tidak dapat diubah)</span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 font-bold mb-3 text-lg">Kode Map / Nomor Arsip</label>
                <div class="relative">
                    <input type="text" name="kode_jenis" value="<?= htmlspecialchars($data['kode_jenis']) ?>" 
                           class="w-full px-6 py-4 pl-14 bg-white border-2 border-amber-200 rounded-2xl focus:border-amber-500 focus:ring-4 focus:ring-amber-100 transition-all text-xl font-mono font-bold text-gray-800 uppercase"
                           placeholder="TU-IV-01">
                    <i class="fas fa-barcode absolute left-5 top-5 text-amber-400 text-xl"></i>
                </div>
                <p class="text-sm text-gray-500 mt-2">Pastikan kode unik dan sesuai format standar.</p>
            </div>

            <div>
                <label class="block text-gray-700 font-bold mb-3 text-lg">Judul / Uraian Arsip</label>
                <input type="text" name="nama_jenis" value="<?= htmlspecialchars($data['nama_jenis']) ?>" 
                       class="w-full px-6 py-4 bg-gray-50 border border-gray-300 rounded-2xl focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-100 transition-all text-lg">
            </div>

            <div>
                <label class="block text-gray-700 font-bold mb-4 text-lg">Kategori Arsip</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach($kategoris as $k): ?>
                        <label class="relative flex items-center p-4 bg-white border-2 border-gray-200 rounded-2xl cursor-pointer hover:border-amber-300 hover:bg-amber-50/50 transition-all shadow-sm group
                                      has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50 has-[:checked]:shadow-md">
                            
                            <input type="radio" name="kategori_id" value="<?= $k['id'] ?>" class="peer sr-only" <?= $data['kategori_id'] == $k['id'] ? 'checked' : '' ?>>
                            
                            <div class="flex items-center gap-4 w-full">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl transition-colors
                                            bg-gray-100 text-gray-500 group-hover:bg-amber-100 group-hover:text-amber-600
                                            peer-checked:bg-amber-500 peer-checked:text-white">
                                    <?php 
                                        $icon = 'fa-folder'; 
                                        if(stripos($k['nama_kategori'], 'teknis') !== false) $icon = 'fa-cogs';
                                        elseif(stripos($k['nama_kategori'], 'administrasi') !== false) $icon = 'fa-building';
                                    ?>
                                    <i class="fas <?= $icon ?>"></i>
                                </div>
                                <span class="font-bold text-gray-700 peer-checked:text-amber-900 text-lg">
                                    <?= htmlspecialchars($k['nama_kategori']) ?>
                                </span>
                            </div>

                            <div class="absolute top-1/2 right-5 -translate-y-1/2 w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center transition-all bg-white
                                        peer-checked:border-amber-500 peer-checked:bg-amber-500">
                                <i class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100"></i>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-gray-700 font-bold mb-3 text-lg">Lokasi Simpan</label>
                    <div class="relative">
                        <input type="text" name="lokasi_simpan" value="<?= htmlspecialchars($data['lokasi_simpan']) ?>" list="list_lokasi" 
                               class="w-full px-6 py-4 pl-12 bg-gray-50 border border-gray-300 rounded-2xl focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-100 transition-all text-lg">
                        <i class="fas fa-map-marker-alt absolute left-5 top-5 text-gray-400 text-xl"></i>
                        <datalist id="list_lokasi">
                            <?php foreach($lokasis as $l): ?>
                                <option value="<?= htmlspecialchars($l['lokasi_simpan']) ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-3 text-lg">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="w-full px-6 py-4 bg-gray-50 border border-gray-300 rounded-2xl focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-100 transition-all text-lg resize-none"><?= htmlspecialchars($data['deskripsi']) ?></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-8 border-t border-gray-100">
                <a href="index.php" class="text-gray-500 hover:text-gray-800 font-bold px-6 py-3 rounded-xl hover:bg-gray-100 transition">Batal</a>
                <button type="submit" class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold py-4 px-10 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition duration-200 flex items-center gap-3 text-lg">
                    <i class="fas fa-save"></i> SIMPAN PERUBAHAN
                </button>
            </div>
        </div>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>