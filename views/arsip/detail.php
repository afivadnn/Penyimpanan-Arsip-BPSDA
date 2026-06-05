<?php
// views/arsip/detail.php
require_once '../../utils/functions.php';
require_once '../../config/database.php';
checkLogin();

if (!isset($_GET['id'])) redirect('views/arsip/index.php');
$id = $_GET['id'];

$db = (new Database())->getConnection();

// 1. AMBIL HEADER ARSIP
$sql = "SELECT d.*, s.kode_subag, s.nama_subag, k.nama_kategori 
        FROM data_arsip d 
        JOIN subag s ON d.subag_id = s.id 
        JOIN kategori_arsip k ON d.kategori_id = k.id 
        WHERE d.id = :id LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();
$arsip = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$arsip) {
    setFlash('danger', 'Data arsip tidak ditemukan.');
    redirect('views/arsip/index.php');
}

// 2. AMBIL LIST BERKAS
$sqlBerkas = "SELECT b.*, u.nama_lengkap as uploader 
              FROM berkas b 
              JOIN users u ON b.uploaded_by = u.id 
              WHERE b.data_arsip_id = :id 
              ORDER BY b.uploaded_at DESC";
$stmtBerkas = $db->prepare($sqlBerkas);
$stmtBerkas->bindParam(':id', $id);
$stmtBerkas->execute();
$list_berkas = $stmtBerkas->fetchAll(PDO::FETCH_ASSOC);
$jumlah_digital = count($list_berkas);

$page_title = "Detail • " . $arsip['kode_jenis'];

include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';
?>

<div class="max-w-7xl mx-auto p-6 lg:p-10">

    <!-- Back Button -->
    <a href="browse.php?subag_id=<?= $arsip['subag_id'] ?>&tahun=<?= $arsip['tahun'] ?>&kategori_id=<?= $arsip['kategori_id'] ?>"
       class="inline-flex items-center gap-3 text-gray-600 hover:text-indigo-700 font-medium text-lg mb-10 transition-all hover:-translate-x-1">
        <i class="fas fa-arrow-left"></i>
        Kembali ke Daftar Arsip
    </a>

    <!-- Hero Header Card -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden mb-12">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-700 p-10 text-white">
            <div class="flex flex-wrap items-center gap-4 mb-6">
                <span class="px-5 py-3 bg-white/20 backdrop-blur-md rounded-2xl font-bold border border-white/30">
                    <?= htmlspecialchars($arsip['kode_subag']) ?>
                </span>
                <span class="px-5 py-3 bg-white/20 backdrop-blur-md rounded-2xl font-bold border border-white/30">
                    Tahun <?= $arsip['tahun'] ?>
                </span>
                <span class="text-3xl font-mono font-extrabold tracking-wider bg-white/25 px-6 py-3 rounded-2xl">
                    <?= htmlspecialchars($arsip['kode_jenis']) ?>
                </span>
            </div>

            <h1 class="text-4xl lg:text-5xl font-extrabold mb-6 leading-tight">
                <?= htmlspecialchars($arsip['nama_jenis']) ?>
            </h1>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
                <div class="flex items-center gap-4">
                    <i class="fas fa-tag text-2xl opacity-90"></i>
                    <div>
                        <p class="text-white/70 text-sm">Kategori</p>
                        <p class="font-bold text-lg"><?= htmlspecialchars($arsip['nama_kategori']) ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <i class="fas fa-building text-2xl opacity-90"></i>
                    <div>
                        <p class="text-white/70 text-sm">Unit Kerja</p>
                        <p class="font-bold text-lg"><?= htmlspecialchars($arsip['nama_subag']) ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <i class="fas fa-map-marker-alt text-2xl opacity-90"></i>
                    <div>
                        <p class="text-white/70 text-sm">Lokasi Simpan</p>
                        <p class="font-bold text-lg"><?= $arsip['lokasi_simpan'] ?: '<em class="opacity-70">Belum dicatat</em>' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Message -->
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 text-indigo-800 p-6 mb-10 rounded-2xl shadow-lg flex items-center justify-between">
            <div class="flex items-center gap-4">
                <i class="fas fa-bell text-2xl text-indigo-600"></i>
                <div>
                    <p class="font-bold text-lg"><?= $_SESSION['flash']['type'] === 'success' ? 'Sukses' : 'Informasi' ?></p>
                    <p class="mt-1"><?= $_SESSION['flash']['message'] ?></p>
                </div>
            </div>
            <button onclick="this.parentElement.remove()" class="text-indigo-600 hover:text-indigo-800 text-xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <!-- Statistik Cards (3 kolom seragam) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        
        <!-- Jumlah Fisik -->
               <!-- Jumlah Fisik (DIRAPIHKAN KHUSUS TOMBOl SIMPAN) -->
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8 flex flex-col h-full">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-6">Jumlah Berkas Fisik</h3>
            <div class="flex-1 flex flex-col justify-between">
                <div>
                    <div class="text-5xl font-extrabold text-gray-900 mb-2"><?= $arsip['jumlah_fisik'] ?></div>
                    <p class="text-gray-600">lembar / buku</p>
                </div>

                <!-- Form Update Jumlah Fisik - DIRAPIHKAN -->
                <form action="<?= BASE_URL ?>controllers/ArsipController.php" method="POST" class="mt-10">
                    <input type="hidden" name="action" value="update_fisik">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    
                    <label class="block text-sm font-semibold text-indigo-700 mb-3">
                        Update jumlah fisik:
                    </label>
                    
                    <div class="flex items-center gap-4">
                        <input type="number" 
                               name="jumlah_fisik" 
                               value="<?= $arsip['jumlah_fisik'] ?>" 
                               min="0"
                               class="w-full px-5 py-4 text-lg font-medium text-gray-900 bg-gray-50 border border-gray-300 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition"
                               placeholder="0">
                        
                        <button type="submit"
                                class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold text-lg rounded-2xl shadow-xl hover:shadow-2xl transform hover:-translate-y-0.5 transition-all duration-300 whitespace-nowrap">
                            <i class="fas fa-save mr-2"></i>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Status Digital -->
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8 flex flex-col h-full">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-6">Status Digitalisasi</h3>
            <div class="flex-1 flex flex-col justify-between">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 rounded-3xl flex items-center justify-center text-4xl shadow-lg
                        <?= $jumlah_digital > 0 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' ?>">
                        <i class="fas <?= $jumlah_digital > 0 ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold text-gray-900">
                            <?= $jumlah_digital > 0 ? 'Tersedia' : 'Belum Ada' ?>
                        </p>
                        <p class="text-gray-600"><?= $jumlah_digital ?> file PDF</p>
                    </div>
                </div>

                <div class="mt-8 p-4 rounded-2xl text-sm font-semibold border
                    <?= ($jumlah_digital >= $arsip['jumlah_fisik'] && $arsip['jumlah_fisik'] > 0) ? 'bg-green-50 text-green-800 border-green-200' :
                       ($jumlah_digital > 0 ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-gray-50 text-gray-700 border-gray-200') ?>">
                    <?php if($arsip['jumlah_fisik'] == 0): ?>
                        <i class="fas fa-info-circle mr-2"></i> Isi jumlah fisik untuk status akurat
                    <?php elseif($jumlah_digital >= $arsip['jumlah_fisik']): ?>
                        <i class="fas fa-check-double mr-2"></i> Digitalisasi Lengkap
                    <?php else: ?>
                        <i class="fas fa-clock mr-2"></i> Proses Digitalisasi Berlangsung
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Deskripsi -->
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8 flex flex-col h-full">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-6">Deskripsi Arsip</h3>
            <div class="flex-1 text-gray-700 leading-relaxed prose prose-sm max-w-none">
                <?= $arsip['deskripsi'] 
                    ? nl2br(htmlspecialchars($arsip['deskripsi'])) 
                    : '<p class="text-gray-400 italic">Tidak ada deskripsi tambahan.</p>' ?>
            </div>
        </div>
    </div>

    <!-- Upload & File List Section -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-10">
        
        <!-- Upload Panel -->
        <div class="xl:col-span-1">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 h-full flex flex-col">
                <div class="text-center mb-8">
                    <div class="w-20 h-20 mx-auto bg-gradient-to-br from-indigo-100 to-purple-100 rounded-3xl flex items-center justify-center text-4xl text-indigo-700 shadow-lg">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <h3 class="text-2xl font-extrabold text-gray-900 mt-6">Upload Scan PDF</h3>
                    <p class="text-gray-500 text-sm mt-2">Format PDF • Maks. 10MB</p>
                </div>

                <form action="<?= BASE_URL ?>controllers/BerkasController.php" method="POST" enctype="multipart/form-data" class="flex-1 flex flex-col">
                    <input type="hidden" name="action" value="upload">
                    <input type="hidden" name="data_arsip_id" value="<?= $id ?>">

                    <div id="drop-zone"
                         class="flex-1 border-3 border-dashed border-gray-300 rounded-3xl p-12 text-center cursor-pointer transition-all hover:border-indigo-400 hover:bg-indigo-50/30 group">
                        <input type="file" name="file_pdf" id="file-input" class="hidden" accept=".pdf" required>
                        <i class="fas fa-file-pdf text-6xl text-gray-300 group-hover:text-indigo-500 transition mb-4"></i>
                        <p class="text-lg font-medium text-gray-700 group-hover:text-indigo-700">Klik atau tarik file ke sini</p>
                        <p class="text-sm text-gray-500 mt-2">Untuk mengunggah dokumen digital</p>
                        <p id="file-name" class="mt-6 text-sm font-mono text-indigo-700 bg-indigo-50 px-4 py-2 rounded-xl hidden"></p>
                    </div>

                    <button type="submit"
                            class="mt-8 w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold text-lg rounded-2xl shadow-xl hover:shadow-2xl transition transform hover:-translate-y-1">
                        Upload File
                    </button>
                </form>
            </div>
        </div>

        <!-- Daftar File -->
        <div class="xl:col-span-2">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden h-full flex flex-col">
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-10 py-6 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-file-pdf text-red-600"></i>
                        File Digital Tersimpan
                    </h3>
                    <span class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-full shadow">
                        <?= count($list_berkas) ?> File
                    </span>
                </div>

                <div class="flex-1 p-8 overflow-y-auto space-y-5">
                    <?php if (count($list_berkas) > 0): ?>
                        <?php foreach ($list_berkas as $file): ?>
                            <div class="bg-gray-50 hover:bg-indigo-50 rounded-2xl p-6 border border-gray-200 hover:border-indigo-300 transition-all flex items-center gap-6">
                                <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center text-3xl text-red-600 shadow">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-900 truncate" title="<?= htmlspecialchars($file['nama_berkas_asli']) ?>">
                                        <?= htmlspecialchars($file['nama_berkas_asli']) ?>
                                    </h4>
                                    <div class="text-sm text-gray-600 mt-2 flex flex-wrap gap-4">
                                        <span><i class="fas fa-hdd mr-1"></i> <?= number_format($file['ukuran_file_kb']) ?> KB</span>
                                        <span><i class="fas fa-calendar mr-1"></i> <?= format_tanggal_indo(date('Y-m-d', strtotime($file['uploaded_at']))) ?></span>
                                        <span><i class="fas fa-user mr-1"></i> <?= htmlspecialchars($file['uploader']) ?></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <a href="<?= BASE_URL ?>view.php?f=<?= $file['nama_file_tersimpan'] ?>" target="_blank"
                                       class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow hover:shadow-lg transition">
                                        Buka
                                    </a>
                                    <?php if ($_SESSION['role'] === 'admin'): ?>
                                        <a href="<?= BASE_URL ?>controllers/BerkasController.php?action=delete&id=<?= $file['id'] ?>"
                                           onclick="return confirm('Hapus file permanen?')"
                                           class="p-3 text-red-600 hover:bg-red-50 rounded-xl transition">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-16 text-gray-400">
                            <i class="fas fa-folder-open text-7xl mb-6 opacity-30"></i>
                            <p class="text-xl font-medium">Belum ada file digital diunggah</p>
                            <p class="text-gray-500 mt-3 max-w-md mx-auto">
                                Anda telah mencatat <strong><?= $arsip['jumlah_fisik'] ?></strong> berkas fisik.<br>
                                Gunakan panel kiri untuk mulai mengunggah scan PDF.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const fileName = document.getElementById('file-name');

    dropZone.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length) {
            fileName.textContent = e.target.files[0].name;
            fileName.classList.remove('hidden');
        }
    });

    ['dragenter', 'dragover'].forEach(event => {
        dropZone.addEventListener(event, (e) => {
            e.preventDefault();
            dropZone.classList.add('border-indigo-500', 'bg-indigo-50/50');
        });
    });

    ['dragleave', 'drop'].forEach(event => {
        dropZone.addEventListener(event, (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-indigo-500', 'bg-indigo-50/50');
        });
    });

    dropZone.addEventListener('drop', (e) => {
        const files = e.dataTransfer.files;
        if (files.length) {
            fileInput.files = files;
            fileName.textContent = files[0].name;
            fileName.classList.remove('hidden');
        }
    });
</script>

<?php include '../../includes/footer.php'; ?>