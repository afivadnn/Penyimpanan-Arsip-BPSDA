<?php
// views/users/input.php
require_once '../../utils/functions.php';
require_once '../../config/database.php';
checkLogin();

// Cek Super Admin
if (!empty($_SESSION['user_subag_id'])) redirect('../dashboard.php');

$db = (new Database())->getConnection();
$subags = $db->query("SELECT * FROM subag ORDER BY kode_subag ASC")->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Tambah User Baru";
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';
?>

<div class="max-w-3xl mx-auto p-6">
    <a href="index.php" class="text-gray-500 hover:text-blue-600 mb-6 inline-block font-medium">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar User
    </a>

    <div class="bg-white rounded-3xl shadow-xl border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6">
            <h1 class="text-2xl font-bold text-white">Buat Akun Baru</h1>
            <p class="text-blue-100 text-sm">Tambahkan petugas untuk mengelola arsip per subag.</p>
        </div>

        <form action="../../controllers/UserController.php" method="POST" class="p-8 space-y-6">
            <input type="hidden" name="action" value="store">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Budi Santoso" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Username</label>
                    <input type="text" name="username" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" placeholder="Tanpa spasi" required>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 font-bold mb-2">Password</label>
                <input type="password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" placeholder="Minimal 6 karakter" required>
            </div>

            <hr class="border-gray-200">

            <div>
                <label class="block text-gray-700 font-bold mb-2">Tipe Akun / Hak Akses</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 p-4 border rounded-xl cursor-pointer hover:bg-gray-50 w-full has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                        <input type="radio" name="role" value="petugas" class="w-5 h-5 text-blue-600" checked onchange="toggleSubag(true)">
                        <div>
                            <span class="font-bold text-gray-800 block">Petugas Subag</span>
                            <span class="text-xs text-gray-500">Hanya bisa akses arsip subag tertentu</span>
                        </div>
                    </label>
                    <label class="flex items-center gap-2 p-4 border rounded-xl cursor-pointer hover:bg-gray-50 w-full has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50">
                        <input type="radio" name="role" value="admin" class="w-5 h-5 text-purple-600" onchange="toggleSubag(false)">
                        <div>
                            <span class="font-bold text-gray-800 block">Super Admin</span>
                            <span class="text-xs text-gray-500">Akses penuh ke semua data</span>
                        </div>
                    </label>
                </div>
            </div>

            <div id="subag-container">
                <label class="block text-gray-700 font-bold mb-2">Pilih Sub Bagian <span class="text-red-500">*</span></label>
                <select name="subag_id" id="subag_select" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white" required>
                    <option value="">-- Pilih Penempatan --</option>
                    <?php foreach($subags as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= $s['kode_subag'] ?> - <?= htmlspecialchars($s['nama_subag']) ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="text-sm text-gray-500 mt-2">User ini hanya akan melihat arsip milik subag yang dipilih.</p>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition transform hover:-translate-y-1">
                    Simpan User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleSubag(show) {
    const container = document.getElementById('subag-container');
    const select = document.getElementById('subag_select');
    
    if (show) {
        container.classList.remove('hidden');
        select.required = true;
    } else {
        container.classList.add('hidden');
        select.required = false;
        select.value = "";
    }
}
</script>

<?php include '../../includes/footer.php'; ?>