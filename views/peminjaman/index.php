<?php
// views/peminjaman/index.php
require_once '../../utils/functions.php';
require_once '../../config/database.php';
require_once '../../models/Peminjaman.php';

checkLogin();

$db = (new Database())->getConnection();
$pinjamModel = new Peminjaman($db);

// --- 1. HITUNG STATISTIK (Badge Counter) ---
// Kita gunakan query manual yang ringan untuk counter
$count_all         = $db->query("SELECT COUNT(*) FROM peminjaman")->fetchColumn();
$count_dipinjam    = $db->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'dipinjam'")->fetchColumn();
$count_dikembalikan= $db->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'dikembalikan'")->fetchColumn();

// --- 2. TANGKAP FILTER & SEARCH ---
$status_filter = $_GET['status'] ?? null;
$search        = $_GET['q'] ?? '';

// --- 3. AMBIL DATA DARI MODEL ---
// Model ini sudah otomatis JOIN ke tabel 'data_arsip' yang baru
$data_pinjam = $pinjamModel->getAll($status_filter, $search);

$page_title = "Peminjaman Arsip Fisik";
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';
?>

<div class="max-w-7xl mx-auto p-6">

    <div class="mb-10">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">Peminjaman Arsip Fisik</h1>
                <p class="text-gray-500 mt-1">Kelola sirkulasi peminjaman map dan berkas fisik.</p>
            </div>
            <a href="form.php" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-xl shadow-lg transition transform hover:-translate-y-1">
                <i class="fas fa-plus-circle"></i>
                Catat Peminjaman Baru
            </a>
        </div>

        <div class="mt-8 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
            
            <div class="inline-flex bg-gray-100 p-1 rounded-xl">
                <a href="index.php" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition <?= !$status_filter ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-600 hover:text-gray-900' ?>">
                    Semua <span class="ml-1 bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded-md text-xs"><?= $count_all ?></span>
                </a>
                <a href="index.php?status=dipinjam" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition <?= $status_filter==='dipinjam' ? 'bg-white text-yellow-700 shadow-sm' : 'text-gray-600 hover:text-gray-900' ?>">
                    Dipinjam <span class="ml-1 bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded-md text-xs"><?= $count_dipinjam ?></span>
                </a>
                <a href="index.php?status=dikembalikan" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition <?= $status_filter==='dikembalikan' ? 'bg-white text-green-700 shadow-sm' : 'text-gray-600 hover:text-gray-900' ?>">
                    Dikembalikan <span class="ml-1 bg-green-100 text-green-700 px-1.5 py-0.5 rounded-md text-xs"><?= $count_dikembalikan ?></span>
                </a>
            </div>

            <form action="" method="GET" class="w-full sm:w-auto relative">
                <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Cari peminjam / kode arsip..." 
                       class="pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none w-full sm:w-80 shadow-sm transition">
                <?php if($status_filter): ?>
                    <input type="hidden" name="status" value="<?= $status_filter ?>">
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-8 rounded shadow-sm flex justify-between items-center">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-xl"></i>
                <div>
                    <p class="font-bold"><?= $_SESSION['flash']['type'] == 'success' ? 'Sukses' : 'Info' ?></p>
                    <p class="text-sm"><?= $_SESSION['flash']['message'] ?></p>
                </div>
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700"><i class="fas fa-times"></i></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <?php if ($data_pinjam->rowCount() > 0): ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            <?php while ($row = $data_pinjam->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group flex flex-col h-full">
                    
                    <div class="p-5 border-b border-gray-50 flex items-start justify-between bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                                <i class="fas fa-folder"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 text-lg leading-tight"><?= htmlspecialchars($row['kode_jenis']) ?></h3>
                                <p class="text-xs text-gray-500 mt-0.5 truncate w-40" title="<?= htmlspecialchars($row['nama_jenis']) ?>">
                                    <?= htmlspecialchars($row['nama_jenis']) ?>
                                </p>
                            </div>
                        </div>
                        
                        <?php if ($row['status'] === 'dipinjam'): ?>
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold border border-yellow-200">
                                Dipinjam
                            </span>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold border border-green-200">
                                Kembali
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="p-5 flex-1 space-y-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-user-circle text-gray-400 text-xl mt-1"></i>
                            <div>
                                <p class="text-sm text-gray-500 uppercase font-bold text-[10px] tracking-wide">Peminjam</p>
                                <p class="font-semibold text-gray-800"><?= htmlspecialchars($row['nama_peminjam_luar']) ?></p>
                                <p class="text-sm text-gray-500"><?= $row['kontak_peminjam'] ?: '-' ?></p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 bg-gray-50 p-3 rounded-xl">
                            <div class="flex-1 text-center border-r border-gray-200 pr-2">
                                <p class="text-[10px] text-gray-500 font-bold uppercase">Pinjam</p>
                                <p class="text-sm font-medium text-blue-700"><?= date('d M Y', strtotime($row['tanggal_pinjam'])) ?></p>
                            </div>
                            <div class="flex-1 text-center pl-2">
                                <p class="text-[10px] text-gray-500 font-bold uppercase">Batas</p>
                                <p class="text-sm font-medium <?= (strtotime($row['tanggal_kembali_rencana']) < time() && $row['status'] == 'dipinjam') ? 'text-red-600 font-bold' : 'text-amber-700' ?>">
                                    <?= date('d M Y', strtotime($row['tanggal_kembali_rencana'])) ?>
                                </p>
                            </div>
                        </div>

                        <?php if($row['catatan_keperluan']): ?>
                        <div class="text-sm text-gray-600 italic bg-yellow-50/50 p-2 rounded border border-yellow-100">
                            "<?= htmlspecialchars($row['catatan_keperluan']) ?>"
                        </div>
                        <?php endif; ?>
                    </div>

                   <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-between items-center gap-2">
                        
                        <div class="flex items-center gap-1">
                        
                            <a href="<?= BASE_URL ?>controllers/PeminjamanController.php?action=delete&id=<?= $row['id'] ?>" 
                               onclick="return confirm('Yakin hapus data peminjaman ini? Log tidak bisa dikembalikan.')"
                               class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition" 
                               title="Hapus Permanen">
                                <i class="fas fa-trash-alt text-sm"></i>
                            </a>
                        </div>

                        <?php if ($row['status'] === 'dipinjam'): ?>
                            <a href="<?= BASE_URL ?>controllers/PeminjamanController.php?action=kembali&id=<?= $row['id'] ?>" 
                               onclick="return confirm('Konfirmasi: Berkas fisik ARSIP <?= $row['kode_jenis'] ?> sudah dikembalikan?')"
                               class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-xs font-bold px-3 py-2 rounded-lg transition shadow-sm">
                                <i class="fas fa-check"></i> Kembali
                            </a>
                        <?php else: ?>
                            <span class="text-green-600 text-xs font-bold flex items-center gap-1 bg-green-50 px-2 py-1 rounded border border-green-100">
                                <i class="fas fa-check-double"></i> Selesai
                            </span>
                        <?php endif; ?>
                        
                    </div>

                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="flex flex-col items-center justify-center py-16 bg-white border-2 border-dashed border-gray-200 rounded-3xl text-center">
            <div class="bg-gray-50 p-4 rounded-full mb-4">
                <i class="fas fa-inbox text-4xl text-gray-300"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-700">Tidak Ada Data Peminjaman</h3>
            <p class="text-gray-500 max-w-sm mx-auto mt-1">Belum ada riwayat peminjaman yang sesuai dengan filter atau pencarian Anda.</p>
            <?php if ($search || $status_filter): ?>
                <a href="index.php" class="mt-4 text-blue-600 hover:text-blue-800 font-medium text-sm">Reset Filter</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

<?php include '../../includes/footer.php'; ?>