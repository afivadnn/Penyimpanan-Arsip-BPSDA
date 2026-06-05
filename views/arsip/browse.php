<?php
// views/arsip/browse.php
require_once '../../utils/functions.php';
require_once '../../config/database.php';
checkLogin();

$db = (new Database())->getConnection();

// TANGKAP PARAMETER
$subag_id    = $_GET['subag_id'] ?? null;
$tahun       = $_GET['tahun'] ?? null;
$kategori_id = $_GET['kategori_id'] ?? null;

// Validasi
if (!$subag_id) redirect('../dashboard.php');

// AMBIL INFO HEADER
$subag_info    = $db->query("SELECT * FROM subag WHERE id = " . (int)$subag_id)->fetch(PDO::FETCH_ASSOC);
$kategori_info = $kategori_id ? $db->query("SELECT * FROM kategori_arsip WHERE id = " . (int)$kategori_id)->fetch(PDO::FETCH_ASSOC) : null;

$page_title = "Arsip • " . $subag_info['nama_subag'];
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';
?>

<div class="w-full max-w-full p-4 md:p-6 lg:p-10 mx-auto">

    <nav class="flex flex-wrap items-center gap-2 text-xs md:text-sm mb-8 bg-white/80 backdrop-blur-sm px-4 py-3 rounded-2xl shadow-sm border border-gray-200 w-fit">
        <a href="../../views/dashboard.php" class="flex items-center gap-1 text-gray-600 hover:text-indigo-700 font-medium transition">
            <i class="fas fa-home"></i>
            <span class="hidden sm:inline">Dashboard</span>
        </a>

        <i class="fas fa-chevron-right text-gray-300 text-[10px]"></i>

        <a href="browse.php?subag_id=<?= $subag_id ?>" 
           class="font-semibold <?= !$tahun ? 'text-indigo-700' : 'text-gray-700 hover:text-indigo-700' ?> transition">
            <?= htmlspecialchars($subag_info['kode_subag']) ?>
        </a>

        <?php if ($tahun): ?>
            <i class="fas fa-chevron-right text-gray-300 text-[10px]"></i>
            <a href="browse.php?subag_id=<?= $subag_id ?>&tahun=<?= $tahun ?>" 
               class="font-semibold <?= !$kategori_id ? 'text-indigo-700' : 'text-gray-700 hover:text-indigo-700' ?> transition">
                <?= $tahun ?>
            </a>
        <?php endif; ?>

        <?php if ($kategori_id): ?>
            <i class="fas fa-chevron-right text-gray-300 text-[10px]"></i>
            <span class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-50 text-indigo-700 font-bold rounded-lg truncate max-w-[150px]">
                <i class="fas fa-tag text-xs"></i>
                <span class="truncate"><?= htmlspecialchars($kategori_info['nama_kategori']) ?></span>
            </span>
        <?php endif; ?>
    </nav>

    <?php if (!$tahun): ?>
        <div class="mb-8">
            <h1 class="text-2xl md:text-4xl font-extrabold text-gray-900">Pilih Tahun</h1>
            <p class="mt-2 text-gray-600">
                Unit Kerja: <span class="font-bold text-indigo-700"><?= htmlspecialchars($subag_info['nama_subag']) ?></span>
            </p>
        </div>

        <?php
        $stmt = $db->prepare("SELECT DISTINCT tahun FROM data_arsip WHERE subag_id = ? ORDER BY tahun DESC");
        $stmt->execute([$subag_id]);
        ?>

        <?php if ($stmt->rowCount() > 0): ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-8">
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <a href="?subag_id=<?= $subag_id ?>&tahun=<?= $row['tahun'] ?>" 
                       class="group block bg-white rounded-3xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden transform hover:-translate-y-2">
                        <div class="h-24 md:h-32 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center group-hover:from-indigo-600 group-hover:to-purple-700 transition-all">
                            <i class="fas fa-calendar-alt text-4xl md:text-6xl text-white/90 group-hover:scale-110 transition-transform duration-500"></i>
                        </div>
                        <div class="p-4 text-center">
                            <p class="text-xl md:text-3xl font-extrabold text-gray-900"><?= $row['tahun'] ?></p>
                            <p class="mt-1 text-[10px] md:text-xs text-gray-500 font-bold uppercase tracking-wide">Buka Folder</p>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-16 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-300">
                <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-700">Belum Ada Arsip</h3>
                <p class="text-gray-500 mb-6 text-sm">Unit ini belum memiliki data arsip.</p>
                <a href="input.php?subag_id=<?= $subag_id ?>" class="px-5 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-lg text-sm">
                    + Input Data Pertama
                </a>
            </div>
        <?php endif; ?>


    <?php elseif (!$kategori_id): ?>
        <div class="mb-8">
            <h1 class="text-2xl md:text-4xl font-extrabold text-gray-900">Pilih Kategori</h1>
            <p class="mt-2 text-gray-600">
                Arsip Tahun <span class="font-bold text-indigo-700"><?= $tahun ?></span>
            </p>
        </div>

        <?php
        $stmt = $db->prepare("SELECT DISTINCT k.id, k.nama_kategori 
                              FROM data_arsip d 
                              JOIN kategori_arsip k ON d.kategori_id = k.id 
                              WHERE d.subag_id = ? AND d.tahun = ?
                              ORDER BY k.nama_kategori ASC");
        $stmt->execute([$subag_id, $tahun]);
        ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-8">
            <?php if ($stmt->rowCount() > 0): ?>
                <?php while ($k = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php
                    $icon = 'fa-folder';
                    if (stripos($k['nama_kategori'], 'keuangan') !== false) $icon = 'fa-file-invoice-dollar';
                    elseif (stripos($k['nama_kategori'], 'teknis') !== false) $icon = 'fa-cogs';
                    elseif (stripos($k['nama_kategori'], 'administrasi') !== false) $icon = 'fa-building';
                    ?>
                    <a href="?subag_id=<?= $subag_id ?>&tahun=<?= $tahun ?>&kategori_id=<?= $k['id'] ?>" 
                       class="group flex items-center gap-4 p-5 md:p-8 bg-white rounded-3xl shadow-md hover:shadow-xl border border-gray-100 transition-all duration-300 hover:border-indigo-200 transform hover:-translate-y-1">
                        <div class="w-14 h-14 md:w-20 md:h-20 bg-indigo-50 rounded-2xl flex items-center justify-center text-2xl md:text-3xl text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                            <i class="fas <?= $icon ?>"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg md:text-xl font-bold text-gray-900 group-hover:text-indigo-700 transition truncate">
                                <?= htmlspecialchars($k['nama_kategori']) ?>
                            </h3>
                            <p class="text-xs md:text-sm text-gray-500 truncate">Klik untuk lihat detail</p>
                        </div>
                        <i class="fas fa-arrow-right text-gray-300 group-hover:text-indigo-600 group-hover:translate-x-2 transition-all"></i>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-16 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-300">
                    <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-700">Kategori Tidak Ditemukan</h3>
                    <p class="text-gray-500 mb-6 text-sm">Tidak ada arsip di tahun <?= $tahun ?>.</p>
                    <a href="input.php?subag_id=<?= $subag_id ?>&tahun=<?= $tahun ?>" class="px-5 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-lg text-sm">
                        + Tambah Arsip Baru
                    </a>
                </div>
            <?php endif; ?>
        </div>


    <?php else: ?>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900">Daftar Arsip</h1>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-100 text-indigo-800 font-bold rounded-lg text-xs md:text-sm">
                        <i class="fas fa-tag"></i> <?= htmlspecialchars($kategori_info['nama_kategori']) ?>
                    </span>
                    <span class="text-gray-500 text-sm">• <?= $tahun ?></span>
                </div>
            </div>

            <a href="input.php?subag_id=<?= $subag_id ?>&tahun=<?= $tahun ?>&kategori_id=<?= $kategori_id ?>"
               class="flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all text-sm">
                <i class="fas fa-plus-circle"></i>
                Input Arsip Baru
            </a>
        </div>

        <?php
        $stmt = $db->prepare("SELECT * FROM data_arsip 
                              WHERE subag_id = ? AND tahun = ? AND kategori_id = ? 
                              ORDER BY kode_jenis ASC");
        $stmt->execute([$subag_id, $tahun, $kategori_id]);
        ?>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden w-full">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Kode Arsip</th>
                            <th class="px-6 py-4 min-w-[300px]">Uraian Arsip</th>
                            <th class="px-6 py-4">Lokasi</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if ($stmt->rowCount() > 0): ?>
                            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr class="hover:bg-indigo-50/40 transition-all duration-200">
                                    <td class="px-6 py-4 align-top">
                                        <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-700 font-mono font-bold rounded-lg text-xs border border-indigo-100">
                                            <?= htmlspecialchars($row['kode_jenis']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 align-top whitespace-normal">
                                        <div class="font-bold text-gray-900 text-sm"><?= htmlspecialchars($row['nama_jenis']) ?></div>
                                        <?php if ($row['deskripsi']): ?>
                                            <p class="text-xs text-gray-500 mt-1 line-clamp-2"><?= htmlspecialchars($row['deskripsi']) ?></p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 align-top text-gray-600 text-sm">
                                        <?php if($row['lokasi_simpan']): ?>
                                            <div class="flex items-center gap-1.5">
                                                <i class="fas fa-map-marker-alt text-red-300"></i>
                                                <?= htmlspecialchars($row['lokasi_simpan']) ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-300 italic">-</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="px-6 py-4 align-top text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="detail.php?id=<?= $row['id'] ?>" 
                                               class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-sm"
                                               title="Lihat Detail">
                                                <i class="fas fa-eye text-xs"></i>
                                            </a>

                                            <a href="edit.php?id=<?= $row['id'] ?>" 
                                               class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-amber-500 flex items-center justify-center hover:bg-amber-500 hover:text-white hover:border-amber-500 transition-all shadow-sm"
                                               title="Edit Data">
                                                <i class="fas fa-pencil-alt text-xs"></i>
                                            </a>

                                            <a href="../../controllers/ArsipController.php?action=delete&id=<?= $row['id'] ?>" 
                                               onclick="return confirm('Yakin hapus arsip <?= $row['id'] ?>? Data tidak bisa dikembalikan.')"
                                               class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white hover:border-red-500 transition-all shadow-sm"
                                               title="Hapus">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="py-16 text-center">
                                    <i class="fas fa-inbox text-6xl text-gray-200 mb-4"></i>
                                    <p class="text-gray-600 font-medium">Belum ada arsip di kategori ini</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php include '../../includes/footer.php'; ?>