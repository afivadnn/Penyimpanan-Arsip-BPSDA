<?php
// views/dashboard.php
require_once '../utils/functions.php';
require_once '../config/database.php';
checkLogin();

$page_title = "Dashboard";
$db = (new Database())->getConnection();

// --- 1. FILTER STATISTIK BERDASARKAN ROLE ---
$filter_sql_arsip = "";
$filter_sql_join  = "";

if (!empty($_SESSION['user_subag_id'])) {
    $subag_id = intval($_SESSION['user_subag_id']);
    $filter_sql_arsip = " WHERE subag_id = $subag_id";
    $filter_sql_join  = " AND d.subag_id = $subag_id";
}

// A. Total Map Arsip
$total_map = $db->query("SELECT COUNT(*) FROM data_arsip" . $filter_sql_arsip)->fetchColumn();

// B. Total Berkas Digital
$sqlDigital = "SELECT COUNT(*) FROM berkas b JOIN data_arsip d ON b.data_arsip_id = d.id WHERE 1=1" . $filter_sql_join;
$total_digital = $db->query($sqlDigital)->fetchColumn();

// C. Total Berkas Fisik
$sqlFisik = "SELECT SUM(jumlah_fisik) FROM data_arsip" . $filter_sql_arsip;
$total_fisik = $db->query($sqlFisik)->fetchColumn();

// D. TOTAL KESELURUHAN
$total_keseluruhan = (int)$total_digital + (int)$total_fisik;

// E. Sedang Dipinjam
$sqlPinjam = "SELECT COUNT(*) FROM peminjaman p JOIN data_arsip d ON p.data_arsip_id = d.id WHERE p.status='dipinjam'" . $filter_sql_join;
$total_pinjam = $db->query($sqlPinjam)->fetchColumn();

// --- 2. AMBIL DATA SUBAG ---
if (empty($_SESSION['user_subag_id'])) {
    $subags = $db->query("SELECT * FROM subag ORDER BY kode_subag ASC")->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmtSub = $db->prepare("SELECT * FROM subag WHERE id = ?");
    $stmtSub->execute([$_SESSION['user_subag_id']]);
    $subags = $stmtSub->fetchAll(PDO::FETCH_ASSOC);
}

include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/navbar.php';
?>

<div class="min-h-screen bg-gray-50 p-6 lg:p-10">

    <!-- Header Selamat Datang -->
    <div class="mb-12">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900">Selamat Datang, <?= $_SESSION['nama_lengkap'] ?? 'Pengguna' ?>!</h1>
                <p class="mt-3 text-lg text-gray-600">Ringkasan arsip digital & fisik hari ini</p>
            </div>
            <div class="mt-6 lg:mt-0">
                <div class="inline-flex items-center gap-3 px-6 py-3 bg-white rounded-xl shadow-sm border border-gray-200">
                    <i class="fas fa-calendar-alt text-indigo-600 text-xl"></i>
                    <span class="font-medium text-gray-800"><?= format_tanggal_indo(date('Y-m-d')) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
        
        <!-- Total Map Arsip -->
        <div class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-xl transition-all duration-500 hover:shadow-2xl hover:-translate-y-2">
            <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
            <div class="relative p-8">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-indigo-100 text-sm font-medium uppercase tracking-wider">Total Map Arsip</p>
                        <p class="mt-4 text-5xl font-extrabold"><?= number_format($total_map) ?></p>
                    </div>
                    <div class="p-5 bg-white/20 rounded-2xl">
                        <i class="fas fa-archive text-4xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Semua Berkas -->
        <div class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-xl transition-all duration-500 hover:shadow-2xl hover:-translate-y-2">
            <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
            <div class="relative p-8">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-emerald-100 text-sm font-medium uppercase tracking-wider">Total Semua Berkas</p>
                        <p class="mt-4 text-5xl font-extrabold"><?= number_format($total_keseluruhan) ?></p>
                        <p class="mt-3 text-sm text-emerald-100 opacity-90">
                            Fisik: <?= number_format($total_fisik) ?> • Digital: <?= number_format($total_digital) ?>
                        </p>
                    </div>
                    <div class="p-5 bg-white/20 rounded-2xl">
                        <i class="fas fa-layer-group text-4xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sedang Dipinjam -->
        <div class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-xl transition-all duration-500 hover:shadow-2xl hover:-translate-y-2">
            <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
            <div class="relative p-8">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-100 text-sm font-medium uppercase tracking-wider">Sedang Dipinjam</p>
                        <p class="mt-4 text-5xl font-extrabold"><?= number_format($total_pinjam) ?></p>
                    </div>
                    <div class="p-5 bg-white/20 rounded-2xl">
                        <i class="fas fa-clock text-4xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Telusuri Per Bagian -->
    <div class="mb-16">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-gray-800 flex items-center gap-4">
                <i class="fas fa-sitemap text-indigo-600 text-2xl"></i>
                Telusuri Arsip Per Bagian
            </h2>
            <span class="text-lg text-gray-500"><?= count($subags) ?> Sub Bagian</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <?php foreach($subags as $sub): 
                $stmt = $db->prepare("SELECT COUNT(*) FROM data_arsip WHERE subag_id = ?");
                $stmt->execute([$sub['id']]);
                $count = $stmt->fetchColumn();
            ?>
                <a href="arsip/browse.php?subag_id=<?= $sub['id'] ?>" class="group block">
                    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8 hover:shadow-2xl hover:border-indigo-200 transition-all duration-500 h-full flex flex-col justify-between transform hover:-translate-y-3">
                        <div class="text-center">
                            <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-3xl flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform duration-500">
                                <span class="text-3xl font-extrabold text-white"><?= strtoupper(substr($sub['kode_subag'], 0, 2)) ?></span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 group-hover:text-indigo-700 transition-colors">
                                <?= htmlspecialchars($sub['kode_subag']) ?>
                            </h3>
                            <p class="mt-2 text-gray-600 leading-relaxed">
                                <?= htmlspecialchars($sub['nama_subag']) ?>
                            </p>
                        </div>
                        <div class="mt-8 text-center">
                            <span class="inline-flex items-center gap-2 px-5 py-3 rounded-full text-sm font-semibold bg-indigo-100 text-indigo-800 shadow-sm">
                                <i class="fas fa-archive"></i>
                                <?= $count ?> Map
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Petunjuk Navigasi -->
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-3xl p-8 flex items-start gap-6 shadow-lg">
        <div class="flex-shrink-0 w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center">
            <i class="fas fa-info-circle text-3xl text-indigo-600"></i>
        </div>
        <div>
            <h4 class="text-xl font-bold text-indigo-900">Petunjuk Navigasi</h4>
            <p class="mt-3 text-indigo-800 leading-relaxed">
                Klik kartu <strong>Sub Bagian</strong> di atas untuk menelusuri arsip secara bertingkat (Tahun → Kategori → Arsip).<br>
                "Total Semua Berkas" merupakan gabungan berkas digital yang diunggah dan jumlah berkas fisik yang dicatat manual.
            </p>
        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>