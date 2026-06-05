<?php
// views/users/index.php
require_once '../../utils/functions.php';
require_once '../../config/database.php';
checkLogin();

// Cek Super Admin
if (!empty($_SESSION['user_subag_id'])) {
    redirect('../dashboard.php');
}

$db = (new Database())->getConnection();

// Ambil data user join ke subag
$query = "SELECT u.*, s.nama_subag 
          FROM users u 
          LEFT JOIN subag s ON u.subag_id = s.id 
          ORDER BY u.subag_id ASC, u.nama_lengkap ASC";
$users = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Ambil data Subag untuk Dropdown di Modal Edit
$subags = $db->query("SELECT * FROM subag ORDER BY kode_subag ASC")->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Manajemen User";
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';
?>

<div class="max-w-7xl mx-auto p-6">
    
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900">Manajemen Pengguna</h1>
            <p class="text-gray-500">Kelola akun akses untuk setiap Sub Bagian.</p>
        </div>
        <a href="input.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition flex items-center gap-2">
            <i class="fas fa-user-plus"></i> Tambah User Baru
        </a>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="bg-blue-50 border-l-4 <?= $_SESSION['flash']['type'] == 'success' ? 'border-green-500 text-green-700' : 'border-red-500 text-red-700' ?> p-4 mb-6 rounded shadow-sm flex justify-between items-center">
            <div><?= $_SESSION['flash']['message'] ?></div>
            <button onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="p-5 font-bold text-gray-600">Nama Lengkap</th>
                    <th class="p-5 font-bold text-gray-600">Username</th>
                    <th class="p-5 font-bold text-gray-600">Hak Akses / Jabatan</th>
                    <th class="p-5 font-bold text-gray-600 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach($users as $u): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-5">
                        <div class="font-bold text-gray-800"><?= htmlspecialchars($u['nama_lengkap']) ?></div>
                        <div class="text-xs text-gray-400">Terdaftar: <?= date('d M Y', strtotime($u['created_at'])) ?></div>
                    </td>
                    <td class="p-5 font-mono text-blue-600"><?= htmlspecialchars($u['username']) ?></td>
                    <td class="p-5">
                        <?php if(empty($u['subag_id'])): ?>
                            <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-bold border border-purple-200">
                                SUPER ADMIN
                            </span>
                        <?php else: ?>
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold border border-blue-200">
                                PETUGAS: <?= htmlspecialchars($u['nama_subag']) ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="p-5 text-center">
                        <div class="flex items-center justify-center gap-2">
                            
                            <button onclick="openEditModal(this)" 
                                    data-id="<?= $u['id'] ?>"
                                    data-nama="<?= htmlspecialchars($u['nama_lengkap']) ?>"
                                    data-username="<?= htmlspecialchars($u['username']) ?>"
                                    data-subag="<?= $u['subag_id'] ?>"
                                    class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition" 
                                    title="Edit Data User">
                                <i class="fas fa-pencil-alt"></i>
                            </button>

                            <button onclick="openResetModal('<?= $u['id'] ?>', '<?= htmlspecialchars($u['nama_lengkap']) ?>')" 
                                    class="text-yellow-600 hover:text-yellow-800 bg-yellow-50 hover:bg-yellow-100 p-2 rounded-lg transition" 
                                    title="Reset Password">
                                <i class="fas fa-key"></i>
                            </button>

                            <?php if($u['id'] != $_SESSION['user_id']): ?>
                                <form action="../../controllers/UserController.php" method="POST" onsubmit="return confirm('Yakin hapus user ini?');" class="inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition" title="Hapus User">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeEditModal()"></div>
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            
            <form action="../../controllers/UserController.php" method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">

                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-user-edit text-blue-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg font-bold leading-6 text-gray-900">Edit Data User</h3>
                            <div class="mt-4 space-y-4">
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap" id="edit_nama" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Username</label>
                                    <input type="text" name="username" id="edit_username" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Akun</label>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                                            <input type="radio" name="role" value="petugas" id="role_petugas" class="text-blue-600" onchange="toggleEditSubag(true)">
                                            Petugas Subag
                                        </label>
                                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                                            <input type="radio" name="role" value="admin" id="role_admin" class="text-purple-600" onchange="toggleEditSubag(false)">
                                            Super Admin
                                        </label>
                                    </div>
                                </div>

                                <div id="edit_subag_container">
                                    <label class="block text-sm font-medium text-gray-700">Sub Bagian</label>
                                    <select name="subag_id" id="edit_subag" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">-- Pilih Sub Bagian --</option>
                                        <?php foreach($subags as $s): ?>
                                            <option value="<?= $s['id'] ?>"><?= $s['kode_subag'] ?> - <?= htmlspecialchars($s['nama_subag']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="submit" class="inline-flex w-full justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto transition">
                        Simpan Perubahan
                    </button>
                    <button type="button" onclick="closeEditModal()" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="resetModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeResetModal()"></div>
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <form action="../../controllers/UserController.php" method="POST">
                <input type="hidden" name="action" value="reset_password">
                <input type="hidden" name="id" id="reset_user_id">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-key text-yellow-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg font-bold leading-6 text-gray-900">Reset Password</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-4">
                                    Masukkan password baru untuk user <span id="reset_user_name" class="font-bold text-gray-800"></span>.
                                </p>
                                <input type="text" name="new_password" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition" placeholder="Ketik Password Baru..." required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="submit" class="inline-flex w-full justify-center rounded-xl bg-yellow-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-yellow-500 sm:ml-3 sm:w-auto transition">
                        Simpan Password
                    </button>
                    <button type="button" onclick="closeResetModal()" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // --- LOGIKA EDIT MODAL ---
    function openEditModal(button) {
        // Ambil data dari tombol
        const id = button.dataset.id;
        const nama = button.dataset.nama;
        const username = button.dataset.username;
        const subag = button.dataset.subag; // Bisa kosong jika admin

        // Isi form
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_username').value = username;

        // Set Role & Subag Logic
        if (subag) {
            document.getElementById('role_petugas').checked = true;
            document.getElementById('edit_subag').value = subag;
            toggleEditSubag(true);
        } else {
            document.getElementById('role_admin').checked = true;
            document.getElementById('edit_subag').value = "";
            toggleEditSubag(false);
        }

        // Tampilkan Modal
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function toggleEditSubag(isPetugas) {
        const container = document.getElementById('edit_subag_container');
        const select = document.getElementById('edit_subag');
        if (isPetugas) {
            container.classList.remove('hidden');
            select.required = true;
        } else {
            container.classList.add('hidden');
            select.required = false;
            select.value = "";
        }
    }

    // --- LOGIKA RESET MODAL ---
    function openResetModal(id, name) {
        document.getElementById('reset_user_id').value = id;
        document.getElementById('reset_user_name').textContent = name;
        document.getElementById('resetModal').classList.remove('hidden');
    }

    function closeResetModal() {
        document.getElementById('resetModal').classList.add('hidden');
    }
</script>

<?php include '../../includes/footer.php'; ?>