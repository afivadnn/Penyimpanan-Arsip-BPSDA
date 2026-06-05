<?php
// views/peminjaman/form.php
require_once '../../utils/functions.php';
require_once '../../config/database.php';
checkLogin();

$db = (new Database())->getConnection();

// 1. DATA SUBAG
$subags = $db->query("SELECT * FROM subag ORDER BY kode_subag ASC")->fetchAll(PDO::FETCH_ASSOC);

// 2. DATA TAHUN (Ambil DISTINCT dari data yang ada, karena tabel tahun_arsip sudah dihapus)
$tahuns = $db->query("SELECT DISTINCT tahun FROM data_arsip ORDER BY tahun DESC")->fetchAll(PDO::FETCH_COLUMN);

// 3. SEMUA ARSIP (Untuk data filtering di JavaScript)
// Perbaikan: Query ke tabel 'data_arsip', ambil kolom 'tahun' bukan 'tahun_id'
$stmt = $db->query("SELECT j.id, j.subag_id, j.tahun, j.kode_jenis, j.nama_jenis, j.lokasi_simpan 
                    FROM data_arsip j 
                    ORDER BY j.created_at DESC");
$all_arsips = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Catat Peminjaman Baru";
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';
?>

<div class="max-w-5xl mx-auto p-6">

    <a href="index.php" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-700 font-medium mb-8 transition">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Peminjaman
    </a>

    <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-3xl shadow-2xl overflow-hidden mb-10 text-white">
        <div class="p-10 relative">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl md:text-5xl font-extrabold mb-4 leading-tight">
                        Catat Peminjaman<br>
                        <span class="text-3xl text-blue-200">Arsip Fisik</span>
                    </h1>
                    <p class="text-lg text-blue-100 max-w-2xl">
                        Pilih arsip → Isi data peminjam → Simpan.
                    </p>
                </div>
                <div class="hidden lg:block opacity-20">
                    <i class="fas fa-hand-holding text-9xl"></i>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="mb-8 p-6 rounded-2xl border-l-4 <?= $_SESSION['flash']['type'] == 'success' ? 'bg-green-50 border-green-500 text-green-700' : 'bg-red-50 border-red-500 text-red-700' ?> flex items-center gap-4 shadow-sm">
            <i class="fas <?= $_SESSION['flash']['type'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> text-3xl"></i>
            <div>
                <p class="font-bold text-lg"><?= $_SESSION['flash']['type'] == 'success' ? 'Berhasil!' : 'Gagal!' ?></p>
                <p><?= $_SESSION['flash']['message'] ?></p>
            </div>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>controllers/PeminjamanController.php" method="POST" class="bg-white rounded-3xl shadow-xl border border-gray-200 overflow-hidden">
        <input type="hidden" name="action" value="store">

        <div class="p-8 lg:p-12 space-y-10">

            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-3xl p-8 border-2 border-blue-200 shadow-inner">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                        1
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">Cari Arsip</h3>
                        <p class="text-gray-600 text-sm">Filter arsip yang tersedia untuk dipinjam</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Sub Bagian</label>
                        <select id="filter_subag" class="w-full px-5 py-4 bg-white border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition text-gray-700">
                            <option value="">-- Pilih Sub Bagian --</option>
                            <?php foreach($subags as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= $s['kode_subag'] ?> - <?= htmlspecialchars($s['nama_subag']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tahun Arsip</label>
                        <select id="filter_tahun" class="w-full px-5 py-4 bg-white border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition text-gray-700">
                            <option value="">-- Pilih Tahun --</option>
                            <?php foreach($tahuns as $t): ?>
                                <option value="<?= $t ?>"><?= $t ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Arsip <span class="text-red-500">*</span></label>
                        <select name="data_arsip_id" id="select_arsip" class="w-full px-5 py-4 bg-gray-100 border border-gray-300 rounded-xl text-gray-500 cursor-not-allowed font-medium" disabled required>
                            <option value="">-- Set Filter Dulu --</option>
                        </select>
                    </div>
                </div>

                <div id="preview-arsip" class="hidden mt-6 p-6 bg-white rounded-2xl border border-blue-200 shadow-lg flex items-center gap-5 animate-fade-in-down">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 text-3xl">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-gray-800" id="preview-kode"></div>
                        <div class="text-gray-600 line-clamp-1" id="preview-judul"></div>
                        <div class="text-xs font-bold text-blue-600 mt-1 uppercase tracking-wide flex items-center gap-1">
                            <i class="fas fa-map-marker-alt"></i> <span id="preview-lokasi"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                
                <div class="space-y-6">
                    <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-md">2</div>
                        Data Peminjam
                    </h3>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Peminjam <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-4 top-4 text-gray-400"></i>
                            <input type="text" name="nama_peminjam" class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:border-green-500 focus:ring-4 focus:ring-green-100 transition" placeholder="Nama Lengkap / Instansi" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kontak / No. HP</label>
                        <div class="relative">
                            <i class="fas fa-phone absolute left-4 top-4 text-gray-400"></i>
                            <input type="text" name="kontak" class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:border-green-500 focus:ring-4 focus:ring-green-100 transition" placeholder="Contoh: 0812...">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Keperluan</label>
                        <textarea name="keperluan" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:border-green-500 focus:ring-4 focus:ring-green-100 transition resize-none" placeholder="Contoh: Audit BPK, Fotocopy, Rapat..."></textarea>
                    </div>
                </div>

                <div class="space-y-6">
                    <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-md">3</div>
                        Jadwal
                    </h3>

                    <div class="bg-amber-50 p-6 rounded-2xl border border-amber-100 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal Pinjam</label>
                            <input type="date" name="tanggal_pinjam" value="<?= date('Y-m-d') ?>" class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl font-bold text-gray-700" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Rencana Kembali <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_kembali" value="<?= date('Y-m-d', strtotime('+3 days')) ?>" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:border-amber-500 focus:ring-4 focus:ring-amber-100 font-bold text-gray-700" required>
                            <p class="text-xs text-amber-600 mt-2"><i class="fas fa-info-circle"></i> Default batas waktu 3 hari kerja.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-6 pt-8 border-t border-gray-100">
                <a href="index.php" class="text-gray-500 hover:text-gray-800 font-bold px-4 py-2 transition">Batal</a>
                <button type="submit" id="btn-simpan" class="bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white font-bold px-10 py-4 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all flex items-center gap-3">
                    <i class="fas fa-save"></i>
                    <span id="btn-text">SIMPAN DATA</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Data JSON dari PHP
const allArsips = <?= json_encode($all_arsips) ?>;

const subagSelect = document.getElementById('filter_subag');
const tahunSelect = document.getElementById('filter_tahun');
const arsipSelect = document.getElementById('select_arsip');
const previewDiv = document.getElementById('preview-arsip');
const previewKode = document.getElementById('preview-kode');
const previewJudul = document.getElementById('preview-judul');
const previewLokasi = document.getElementById('preview-lokasi');

// Fungsi Filter
function updateArsipList() {
    const subagId = subagSelect.value;
    const tahunVal = tahunSelect.value; // Ini sekarang ANGKA TAHUN (misal: 2025)

    // Reset dropdown
    arsipSelect.innerHTML = '<option value="">-- Pilih Arsip --</option>';
    arsipSelect.disabled = true;
    arsipSelect.classList.add('bg-gray-100', 'cursor-not-allowed');
    previewDiv.classList.add('hidden');

    if (subagId && tahunVal) {
        // FILTER JS: Bandingkan subag_id dan tahun (kolom 'tahun' di data_arsip)
        const filtered = allArsips.filter(a => a.subag_id == subagId && a.tahun == tahunVal);
        
        if (filtered.length > 0) {
            filtered.forEach(arsip => {
                const opt = document.createElement('option');
                opt.value = arsip.id; // Value = ID String (arsip-xxxx)
                opt.textContent = `[${arsip.kode_jenis}] ${arsip.nama_jenis}`;
                opt.dataset.lokasi = arsip.lokasi_simpan || 'Lokasi belum diset';
                opt.dataset.kode = arsip.kode_jenis;
                opt.dataset.judul = arsip.nama_jenis;
                arsipSelect.appendChild(opt);
            });
            
            // Aktifkan dropdown
            arsipSelect.disabled = false;
            arsipSelect.classList.remove('bg-gray-100', 'cursor-not-allowed');
            arsipSelect.classList.add('bg-white');
        } else {
            arsipSelect.innerHTML = '<option value="">-- Tidak ada arsip ditemukan --</option>';
        }
    }
}

// Event Listeners
subagSelect.addEventListener('change', updateArsipList);
tahunSelect.addEventListener('change', updateArsipList);

// Preview saat arsip dipilih
arsipSelect.addEventListener('change', () => {
    const selected = arsipSelect.selectedOptions[0];
    if (selected && selected.value) {
        previewDiv.classList.remove('hidden');
        previewKode.textContent = selected.dataset.kode;
        previewJudul.textContent = selected.dataset.judul;
        previewLokasi.textContent = selected.dataset.lokasi;
    } else {
        previewDiv.classList.add('hidden');
    }
});

// Loading Button Animation
document.querySelector('form').addEventListener('submit', function() {
    const btn = document.getElementById('btn-simpan');
    const txt = document.getElementById('btn-text');
    btn.disabled = true;
    btn.classList.add('opacity-75', 'cursor-not-allowed');
    txt.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> MENYIMPAN...';
});
</script>

<?php include '../../includes/footer.php'; ?>