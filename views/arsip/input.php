<?php
// views/arsip/input.php
require_once '../../utils/functions.php';
require_once '../../config/database.php';
checkLogin();

$db = (new Database())->getConnection();

// 1. AMBIL DATA MASTER
$kategoris = $db->query("SELECT * FROM kategori_arsip")->fetchAll(PDO::FETCH_ASSOC);

// Subag Logic
if (empty($_SESSION['user_subag_id'])) {
    $subags = $db->query("SELECT * FROM subag ORDER BY kode_subag ASC")->fetchAll(PDO::FETCH_ASSOC);
    $is_super_admin = true;
} else {
    $stmt = $db->prepare("SELECT * FROM subag WHERE id = ?");
    $stmt->execute([$_SESSION['user_subag_id']]);
    $subags = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $is_super_admin = false;
}

// Lokasi Suggestion
$lokasis = $db->query("SELECT DISTINCT lokasi_simpan FROM data_arsip WHERE lokasi_simpan != '' AND lokasi_simpan IS NOT NULL ORDER BY lokasi_simpan")->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Input Map Arsip Baru";
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';
?>

<div class="max-w-5xl mx-auto p-6 pb-20">

    <a href="index.php" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-700 font-medium mb-8 transition group">
        <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i> Kembali
    </a>

    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-3xl shadow-xl p-8 text-white mb-10 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold mb-3">Buat Map Arsip Baru</h1>
                <p class="text-blue-100 text-lg">Langkah 1 • Isi informasi dasar map arsip</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-folder-plus text-8xl opacity-30"></i>
            </div>
        </div>
        
        <div class="relative z-10 mt-8 flex items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white text-blue-700 rounded-full flex items-center justify-center font-bold text-lg shadow-lg">1</div>
                <span class="text-base font-bold text-white">Informasi Map</span>
            </div>
            <div class="w-16 h-1 bg-blue-400/50 rounded-full"></div>
            <div class="flex items-center gap-3 opacity-60">
                <div class="w-10 h-10 border-2 border-white/30 rounded-full flex items-center justify-center font-bold text-lg">2</div>
                <span class="text-base">Upload Berkas</span>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="mb-8 bg-red-50 border-l-4 border-red-500 text-red-700 p-5 rounded-r-xl flex items-start gap-4 animate-bounce-slow shadow-sm">
            <i class="fas fa-exclamation-triangle text-2xl mt-1"></i>
            <div>
                <p class="font-bold text-lg">Gagal menyimpan data!</p>
                <p><?= $_SESSION['flash']['message'] ?? '' ?></p>
            </div>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>controllers/ArsipController.php" method="POST" class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
        <input type="hidden" name="action" value="store">

        <div class="p-8 lg:p-12 space-y-10">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-gray-700 font-bold mb-3 text-lg">Sub Bagian <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="subag_id" id="subag" 
                                class="w-full px-6 py-4 bg-gray-50 border border-gray-300 rounded-2xl focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100 transition-all text-lg appearance-none cursor-pointer" 
                                <?= !$is_super_admin ? 'readonly' : '' ?> required>
                            <?php if($is_super_admin): ?>
                                <option value="">-- Pilih Sub Bagian --</option>
                            <?php endif; ?>
                            
                            <?php foreach($subags as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= (!$is_super_admin) ? 'selected' : '' ?>>
                                    <?= $s['kode_subag'] ?> - <?= htmlspecialchars($s['nama_subag']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-6 pointer-events-none text-gray-500">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-bold mb-3 text-lg">Tahun Arsip <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="number" name="tahun" value="<?= date('Y') ?>" min="1900" max="2100" 
                               class="w-full px-6 py-4 pl-12 bg-gray-50 border border-gray-300 rounded-2xl focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100 transition-all text-lg font-bold text-gray-700" 
                               placeholder="Contoh: 2025" required>
                        <i class="fas fa-calendar-alt absolute left-5 top-5 text-gray-400 text-xl"></i>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 ml-1">Masukkan 4 digit tahun (Contoh: 2024)</p>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 font-bold mb-4 text-lg">Kategori Arsip <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach($kategoris as $k): ?>
                        <label class="relative flex items-center p-5 bg-white border-2 border-gray-200 rounded-2xl cursor-pointer hover:border-blue-300 hover:bg-blue-50/50 transition-all shadow-sm group
                                      has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:shadow-md has-[:checked]:ring-2 has-[:checked]:ring-blue-200">
                            
                            <input type="radio" name="kategori_id" value="<?= $k['id'] ?>" class="peer sr-only" required>
                            
                            <div class="flex items-center gap-4 w-full">
                                <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl transition-colors
                                            bg-gray-100 text-gray-500 group-hover:bg-blue-100 group-hover:text-blue-600
                                            peer-checked:bg-blue-600 peer-checked:text-white">
                                    <?php 
                                        $icon = 'fa-folder'; 
                                        if(stripos($k['nama_kategori'], 'teknis') !== false) $icon = 'fa-cogs';
                                        elseif(stripos($k['nama_kategori'], 'administrasi') !== false) $icon = 'fa-building';
                                    ?>
                                    <i class="fas <?= $icon ?>"></i>
                                </div>
                                <div>
                                    <span class="block font-bold text-lg text-gray-700 peer-checked:text-blue-800 transition-colors">
                                        <?= htmlspecialchars($k['nama_kategori']) ?>
                                    </span>
                                    <span class="text-sm text-gray-500">Klik untuk memilih</span>
                                </div>
                            </div>

                            <div class="absolute top-1/2 right-5 -translate-y-1/2 w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center transition-all bg-white
                                        peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:scale-110">
                                <i class="fas fa-check text-white text-sm opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                <div>
                    <label class="block text-gray-700 font-bold mb-3 text-lg">Kode Katalog <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" name="kode_jenis" 
                               class="w-full px-6 py-4 pl-14 bg-gray-50 border border-gray-300 rounded-2xl focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100 transition-all text-lg font-mono tracking-widest uppercase font-bold text-blue-900" 
                               placeholder="TU-01" 
                               oninput="this.value = this.value.toUpperCase()" required>
                        <i class="fas fa-barcode absolute left-5 top-5 text-gray-400 text-xl"></i>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 ml-1">Kode otomatis dikapitalisasi.</p>
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-gray-700 font-bold mb-3 text-lg">Judul / Uraian Arsip <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_jenis" class="w-full px-6 py-4 bg-gray-50 border border-gray-300 rounded-2xl focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100 transition-all text-lg" placeholder="Contoh: Laporan Keuangan Triwulan I" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-gray-700 font-bold mb-3 text-lg">Lokasi Simpan Fisik</label>
                    <div class="relative">
                        <input type="text" name="lokasi_simpan" list="lokasi-suggestions" 
                               class="w-full px-6 py-4 pl-14 bg-gray-50 border border-gray-300 rounded-2xl focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100 transition-all text-lg" 
                               placeholder="Contoh: Lemari A - Rak 2">
                        <i class="fas fa-map-marker-alt absolute left-5 top-5 text-red-400 text-xl"></i>
                    </div>
                    <datalist id="lokasi-suggestions">
                        <?php foreach($lokasis as $l): ?>
                            <option value="<?= htmlspecialchars($l['lokasi_simpan']) ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 font-bold mb-3 text-lg">Deskripsi / Keterangan</label>
                <textarea name="deskripsi" rows="3" class="w-full px-6 py-4 bg-gray-50 border border-gray-300 rounded-2xl focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100 transition-all text-lg resize-none" placeholder="Tambahkan catatan penting..."></textarea>
            </div>

            <div class="flex flex-col-reverse md:flex-row md:items-center md:justify-end gap-4 pt-8 border-t border-gray-100">
                <a href="index.php" class="text-center md:text-left text-gray-500 hover:text-gray-800 font-bold text-lg px-6 py-3 rounded-xl hover:bg-gray-100 transition w-full md:w-auto">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-4 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all text-lg flex items-center justify-center gap-3 w-full md:w-auto">
                    <i class="fas fa-save"></i>
                    SIMPAN & LANJUT
                    <i class="fas fa-arrow-right text-sm"></i>
                </button>
            </div>

        </div>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>