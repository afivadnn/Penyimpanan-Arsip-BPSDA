<?php
// views/arsip/index.php
require_once '../../utils/functions.php';
require_once '../../config/database.php';
require_once '../../models/Arsip.php';

checkLogin();

$db = (new Database())->getConnection();
$arsipModel = new Arsip($db);

// --- 1. LOGIKA FILTER ---
$keyword      = $_GET['q'] ?? '';
$filter_tahun = $_GET['tahun'] ?? '';
$filter_subag = $_GET['subag'] ?? ''; 

if (!empty($_SESSION['user_subag_id'])) {
    $filter_subag = $_SESSION['user_subag_id'];
}

// --- 2. AMBIL DATA ---
$stmt = $arsipModel->getAll([
    'keyword'  => $keyword,
    'tahun'    => $filter_tahun,
    'subag_id' => $filter_subag
]);
$all_arsip = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_data = count($all_arsip);

// --- 3. OPSI FILTER DROPDOWN ---
$tahuns = $db->query("SELECT DISTINCT tahun FROM data_arsip ORDER BY tahun DESC")->fetchAll(PDO::FETCH_COLUMN);

$subags = [];
if (empty($_SESSION['user_subag_id'])) {
    $subags = $db->query("SELECT * FROM subag ORDER BY kode_subag ASC")->fetchAll(PDO::FETCH_ASSOC);
}

$page_title = "Data Arsip";
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';
?>

<div class="w-full max-w-full px-4 py-6 lg:p-10 mx-auto overflow-x-hidden">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 md:mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Data Arsip</h1>
            <p class="text-gray-500 mt-1 text-sm leading-relaxed">
                <?php if(empty($_SESSION['user_subag_id'])): ?>
                    Menampilkan seluruh data arsip Balai PSDA.
                <?php else: ?>
                    Kelola data arsip unit kerja Anda.
                <?php endif; ?>
                <span class="inline-block font-bold text-gray-700 bg-gray-100 px-2 py-0.5 rounded-full text-xs ml-1 mt-1">
                    <?= $total_data ?> Data
                </span>
            </p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <a href="<?= BASE_URL ?>controllers/export_arsip.php?q=<?= urlencode($keyword) ?>&tahun=<?= $filter_tahun ?>&subag=<?= $filter_subag ?>" 
               target="_blank" 
               class="flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-200 transition-all hover:-translate-y-1 w-full sm:w-auto">
                <i class="fas fa-file-excel"></i>
                <span>Export Excel</span>
            </a>

            <a href="input.php" 
               class="flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-200 transition-all hover:-translate-y-1 w-full sm:w-auto">
                <i class="fas fa-plus"></i>
                <span>Input Arsip</span>
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-xl shadow-sm flex justify-between items-start animate-fade-in-down w-full">
            <div class="flex items-start gap-3">
                <div class="bg-blue-100 p-2 rounded-full text-blue-600 flex-shrink-0 mt-0.5">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="min-w-0"> <p class="font-bold text-blue-900 text-sm">Informasi Sistem</p>
                    <p class="text-blue-700 text-sm break-words"><?= $_SESSION['flash']['message'] ?></p>
                </div>
            </div>
            <button onclick="this.parentElement.remove()" class="text-blue-400 hover:text-blue-600 p-1"><i class="fas fa-times"></i></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div class="bg-white p-4 md:p-5 rounded-2xl shadow-sm border border-gray-200 mb-6 w-full">
        <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            
            <div class="md:col-span-12 lg:col-span-6">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Pencarian</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                    </div>
                    <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>" 
                           placeholder="Cari Judul / Kode..." 
                           class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm font-medium text-gray-700">
                </div>
            </div>

            <div class="md:col-span-6 lg:col-span-2">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Tahun</label>
                <div class="relative">
                    <select name="tahun" onchange="this.form.submit()" 
                            class="w-full pl-4 pr-8 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm font-medium appearance-none cursor-pointer">
                        <option value="">Semua Tahun</option>
                        <?php 
                        if(empty($tahuns)) echo "<option value='".date('Y')."'>".date('Y')."</option>";
                        foreach($tahuns as $t): 
                        ?>
                            <option value="<?= $t ?>" <?= ($filter_tahun == $t) ? 'selected' : '' ?>><?= $t ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <div class="md:col-span-6 lg:col-span-4">
                <?php if (empty($_SESSION['user_subag_id'])): ?>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Unit Kerja</label>
                    <div class="relative">
                        <select name="subag" onchange="this.form.submit()" 
                                class="w-full pl-4 pr-8 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm font-medium appearance-none cursor-pointer">
                            <option value="">Semua Bagian</option>
                            <?php foreach($subags as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= ($filter_subag == $s['id']) ? 'selected' : '' ?>>
                                    <?= $s['kode_subag'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                <?php else: ?>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1 invisible">Action</label>
                    <a href="index.php" class="flex items-center justify-center gap-2 w-full py-2.5 bg-white border border-gray-300 text-gray-600 font-bold rounded-xl hover:bg-gray-50 hover:text-gray-800 transition shadow-sm text-sm">
                        <i class="fas fa-sync-alt"></i> Reset Filter
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden w-full">
        <?php if ($total_data > 0): ?>
        
        <div class="overflow-x-auto w-full"> <table class="w-full text-left border-collapse whitespace-nowrap"> <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 font-bold tracking-wider">
                        <th class="px-6 py-4">ID Arsip</th>
                        <th class="px-6 py-4 min-w-[300px]">Uraian Arsip</th>
                        <th class="px-6 py-4 text-center">Tahun</th>
                        <th class="px-6 py-4 text-center">Kategori</th>
                        <th class="px-6 py-4 text-center">Bagian</th>
                        <th class="px-6 py-4 text-center">Digital</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach($all_arsip as $row): 
                        $hasFile = isset($row['jumlah_digital']) && $row['jumlah_digital'] > 0;
                    ?>
                    <tr class="hover:bg-blue-50/40 transition duration-150 group">
                        
                        <td class="px-6 py-4 align-top">
                            <div class="font-mono font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg text-xs inline-block border border-blue-100">
                                <?= htmlspecialchars($row['id']) ?>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 align-top whitespace-normal">
                            <div class="mb-1">
                                <span class="font-bold text-gray-800 text-sm"><?= htmlspecialchars($row['kode_jenis']) ?></span>
                            </div>
                            <div class="text-gray-600 text-sm leading-relaxed line-clamp-2" title="<?= htmlspecialchars($row['nama_jenis']) ?>">
                                <?= htmlspecialchars($row['nama_jenis']) ?>
                            </div>
                            <?php if(!empty($row['lokasi_simpan'])): ?>
                                <div class="mt-2 flex items-center gap-1.5 text-xs text-gray-400 font-medium whitespace-nowrap">
                                    <i class="fas fa-map-marker-alt text-red-300"></i> 
                                    <span><?= htmlspecialchars($row['lokasi_simpan']) ?></span>
                                </div>
                            <?php endif; ?>
                        </td>
                        
                        <td class="px-6 py-4 align-top text-center">
                            <span class="font-bold text-gray-600 bg-gray-100 px-3 py-1 rounded-full text-xs">
                                <?= htmlspecialchars($row['tahun']) ?>
                            </span>
                        </td>

                        <td class="px-6 py-4 align-top text-center">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                <?= htmlspecialchars($row['nama_kategori']) ?>
                            </span>
                        </td>

                        <td class="px-6 py-4 align-top text-center">
                            <span class="font-bold text-xs text-gray-700 uppercase tracking-wide border-b-2 border-yellow-400 pb-0.5">
                                <?= htmlspecialchars($row['kode_subag']) ?>
                            </span>
                        </td>

                        <td class="px-6 py-4 align-top text-center">
                            <?php if($hasFile): ?>
                                <a href="detail.php?id=<?= $row['id'] ?>" class="group/pdf inline-flex flex-col items-center gap-1">
                                    <div class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center group-hover/pdf:bg-red-500 group-hover/pdf:text-white transition-all shadow-sm">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <span class="text-[10px] font-bold text-red-500">Ada File</span>
                                </a>
                            <?php else: ?>
                                <div class="inline-flex flex-col items-center gap-1 opacity-40">
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 text-gray-400 flex items-center justify-center">
                                        <i class="fas fa-times"></i>
                                    </div>
                                    <span class="text-[10px] font-medium text-gray-400">Kosong</span>
                                </div>
                            <?php endif; ?>
                        </td>

                        <td class="px-6 py-4 align-top text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="detail.php?id=<?= $row['id'] ?>" 
                                   class="w-9 h-9 rounded-xl bg-white border border-gray-200 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-sm" 
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <a href="edit.php?id=<?= $row['id'] ?>" 
                                   class="w-9 h-9 rounded-xl bg-white border border-gray-200 text-amber-500 flex items-center justify-center hover:bg-amber-500 hover:text-white hover:border-amber-500 transition-all shadow-sm" 
                                   title="Edit Data">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>

                                <a href="../../controllers/ArsipController.php?action=delete&id=<?= $row['id'] ?>" 
                                   onclick="return confirm('Yakin hapus arsip <?= $row['id'] ?>? Data tidak bisa dikembalikan.')" 
                                   class="w-9 h-9 rounded-xl bg-white border border-gray-200 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white hover:border-red-500 transition-all shadow-sm" 
                                   title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php else: ?>
            <div class="flex flex-col items-center justify-center py-20 px-4 text-center">
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6 animate-bounce-slow">
                    <i class="fas fa-folder-open text-4xl text-gray-300"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Tidak ada data arsip ditemukan</h3>
                <p class="text-gray-500 max-w-md mx-auto mb-8">
                    Coba sesuaikan kata kunci pencarian atau filter Anda. Atau tambahkan arsip baru jika belum ada.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                    <a href="index.php" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition shadow-sm w-full sm:w-auto">
                        Reset Filter
                    </a>
                    <a href="input.php" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-200 w-full sm:w-auto">
                        <i class="fas fa-plus mr-2"></i> Input Baru
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>