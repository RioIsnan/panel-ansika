<?php 
include "cek_login.php"; 
include "koneksi.php";

// Logika Filter & Pagination Anda tetap sama
$limit = 10;
$page = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$offset = ($page > 1) ? ($page * $limit) - $limit : 0;
$tab = $_GET['tab'] ?? 'kuis';
$nama_tabel = ($tab == 'kuis') ? 'tabel_kuis' : 'tabel_cocok_kata';
$filter_level = $_GET['f_level'] ?? '';
$filter_jenis = $_GET['f_jenis'] ?? ''; 
$search = $_GET['q'] ?? '';

$where_clause = " WHERE 1=1";
if ($filter_level != '') $where_clause .= " AND level = '$filter_level'";
if ($filter_jenis != '') {
    if ($tab == 'kuis') { $where_clause .= " AND pertanyaan LIKE '$filter_jenis%'"; } 
    else { $where_clause .= " AND jenis = '$filter_jenis'"; }
}
if ($search != '') {
    $kolom = ($tab == 'kuis') ? 'pertanyaan' : 'kata_soal';
    $where_clause .= " AND $kolom LIKE '%$search%'";
}

$total_data_res = $conn->query("SELECT COUNT(*) AS total FROM $nama_tabel $where_clause");
$total_data = $total_data_res->fetch_assoc()['total'];
$total_halaman = ceil($total_data / $limit);
$result = $conn->query("SELECT * FROM $nama_tabel $where_clause ORDER BY id DESC LIMIT $limit OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data - ANSIKA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="p-4 text-center border-bottom border-secondary mb-3">
        <h3 class="text-info fw-bold mb-0">ANSIKA</h3>
    </div>
    <a href="dashboard.php" class="nav-link"><i class="bi bi-grid-1x2 me-3"></i> Dashboard</a>
    <a href="index.php?tab=kuis" class="nav-link <?= $tab == 'kuis' ? 'active' : '' ?>"><i class="bi bi-pencil-square me-3"></i> Kelola Kuis</a>
    <a href="index.php?tab=cocok kata" class="nav-link <?= $tab == 'cocok_kata' ? 'active' : '' ?>"><i class="bi bi-intersect me-3"></i> Cocok Kata</a>
    <div style="position: absolute; bottom: 50px; width: 100%;">
        <a href="logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-left me-3"></i> Keluar</a>
    </div>
</div>

<div class="main-content">
    <nav class="d-flex justify-content-between align-items-center mb-4">
    <button class="btn btn-white shadow-sm d-lg-none" id="menuToggle">
        <i class="bi bi-list fs-4"></i>
    </button>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <div class="ms-auto d-flex align-items-center">
        <span class="me-3 d-none d-sm-inline text-muted">Halo, <strong><?= $_SESSION['admin'] ?></strong></span>
        <img src="https://ui-avatars.com/api/?name=<?= $_SESSION['admin'] ?>&background=random" class="rounded-circle" width="35">
    </div>
</nav>
    <div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog">
        <form action="import_csv.php" method="POST" enctype="multipart/form-data" class="modal-content border-0">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Import Data dari CSV</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="tab" value="<?= $tab ?>">
                <div class="alert alert-info">
                    <small>
                        Pastikan urutan kolom CSV sesuai: <br>
                        <strong>Kuis:</strong> pertanyaan, pilihan_0, pilihan_1, pilihan_2, pilihan_3, jawaban_index, level <br>
                        <strong>Cocok Kata:</strong> kata_soal, kata_jawaban, jenis, level
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pilih File CSV</label>
                    <input type="file" name="file_csv" class="form-control" accept=".csv" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="import" class="btn btn-success w-100">Proses Import</button>
            </div>
        </form>
    </div>
</div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Manajemen <?= ucfirst($tab) ?></h2>
        <div>
             <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalImport">Import CSV</button>
             <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Data</button>
        </div>
    </div>
    <?php if(isset($_GET['msg'])): ?>
    <?php 
        $msg = $_GET['msg'];
        
        // Pengaturan Warna dan Ikon berdasarkan jenis pesan
        if($msg == 'success') {
            $class = 'alert-success';
            $icon = 'bi-check-circle-fill';
            $teks = "Berhasil! Data baru telah ditambahkan.";
        } elseif($msg == 'updated') {
            $class = 'alert-warning'; // Warna orange/kuning untuk edit
            $icon = 'bi-info-circle-fill';
            $teks = "Berhasil! Data telah diperbarui.";
        } elseif($msg == 'deleted') {
            $class = 'alert-danger';
            $icon = 'bi-trash-fill';
            $teks = "Berhasil! Data telah dihapus.";
        } elseif($msg == 'duplicate') {
            $class = 'alert-danger'; // Merah karena error/gagal
            $icon = 'bi-exclamation-octagon-fill';
            $teks = "Gagal! Data tersebut sudah ada di database (Duplikat).";
        } elseif($msg == 'error') {
            $class = 'alert-danger';
            $icon = 'bi-x-circle-fill';
            $teks = "Terjadi kesalahan sistem.";
        }
    ?>

    <div id="alert-auto-close" class="alert <?= $class ?> alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi <?= $icon ?> me-2 fs-5"></i>
            <div>
                <?= $teks ?>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <script>
        setTimeout(function() {
            var alertElement = document.getElementById('alert-auto-close');
            if (alertElement) {
                var bsAlert = new bootstrap.Alert(alertElement);
                bsAlert.close();
            }
        }, 3000);
    </script>
<?php endif; ?>

    <div class="table-container mb-4">
        <form method="GET" class="row g-2">
            <input type="hidden" name="tab" value="<?= $tab ?>">
            <div class="col-md-4"><input type="text" name="q" class="form-control" placeholder="Cari..." value="<?= $search ?>"></div>
            <div class="col-md-3">
                <select name="f_jenis" class="form-select">
                    <option value="">Semua Jenis</option>
                    <option value="Sinonim" <?= $filter_jenis == 'Sinonim' ? 'selected' : '' ?>>Sinonim</option>
                    <option value="Antonim" <?= $filter_jenis == 'Antonim' ? 'selected' : '' ?>>Antonim</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="f_level" class="form-select">
                    <option value="">Semua Level</option>
                    <option value="1" <?= $filter_level == '1' ? 'selected' : '' ?>>Level 1</option>
                    <option value="2" <?= $filter_level == '2' ? 'selected' : '' ?>>Level 2</option>
                    <option value="3" <?= $filter_level == '3' ? 'selected' : '' ?>>Level 3</option>
                </select>
            </div>
            <div class="col-md-2"><button type="submit" class="btn btn-dark w-100">Filter</button></div>
        </form>
    </div>

    <div class="table-container shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr><th width="60">NO</th><th>ISI DATA</th><th>LEVEL</th><th class="text-center">AKSI</th></tr>
                </thead>
                <tbody>
                    <?php 
                    $no = $offset + 1; 
                    while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= ($tab == 'kuis') ? $row['pertanyaan'] : $row['kata_soal'].' ➔ '.$row['kata_jawaban'] ?></strong></td>
                        <td><span class="badge bg-secondary">Lvl <?= $row['level'] ?></span></td>
                        
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary" 
            data-bs-toggle="modal" 
            data-bs-target="#modalEdit<?= $row['id'] ?>">
        <i class="bi bi-pencil"></i>
    </button>
                            <a href="javascript:void(0)" 
   class="btn btn-sm btn-outline-danger" 
   onclick="konfirmasiHapus('<?= $tab ?>', '<?= $row['id'] ?>')">
   <i class="bi bi-trash"></i>
</a>
                        </td>
                        <div class="modal fade" id="modalEdit<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content text-start">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="edit.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="tab" value="<?= $tab ?>">

                    <?php if($tab == 'kuis'): ?>
    <div class="mb-3">
        <label class="form-label">Jenis Kuis</label>
        <select name="jenis_kuis" class="form-select" required>
            <option value="Sinonim">Sinonim</option>
            <option value="Antonim">Antonim</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Pertanyaan/Soal</label>
        <input type="text" name="pertanyaan" class="form-control" placeholder="Contoh: Besar" required>
    </div>
    <div class="row">
        <div class="col-6 mb-2">
            <label class="small">Pilihan A</label>
            <input type="text" name="pil_a" class="form-control form-control-sm" required>
        </div>
        <div class="col-6 mb-2">
            <label class="small">Pilihan B</label>
            <input type="text" name="pil_b" class="form-control form-control-sm" required>
        </div>
        <div class="col-6 mb-2">
            <label class="small">Pilihan C</label>
            <input type="text" name="pil_c" class="form-control form-control-sm" required>
        </div>
        <div class="col-6 mb-2">
            <label class="small">Pilihan D</label>
            <input type="text" name="pil_d" class="form-control form-control-sm" required>
        </div>
    </div>
    <div class="mb-3 mt-2">
        <label class="form-label">Jawaban Benar</label>
        <select name="jawaban" class="form-select">
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
        </select>
    </div>
                    <?php else: ?>
                        <div class="mb-3">
                            <label class="form-label text-muted">Kata Soal</label>
                            <input type="text" name="kata_soal" class="form-control" value="<?=$row['kata_soal']?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Kata Jawaban</label>
                            <input type="text" name="kata_jawaban" class="form-control" value="<?=$row['kata_jawaban']?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Jenis</label>
                            <select name="jenis" class="form-select">
                                <option value="Sinonim" <?= $row['jenis'] == 'Sinonim' ? 'selected' : '' ?>>Sinonim</option>
                                <option value="Antonim" <?= $row['jenis'] == 'Antonim' ? 'selected' : '' ?>>Antonim</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label text-muted">Level</label>
                        <select name="level" class="form-select">
                            <?php for($i=1; $i<=3; $i++): ?>
                                <option value="<?= $i ?>" <?= $row['level'] == $i ? 'selected' : '' ?>>Level <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php $qa = "&tab=$tab&q=$search&f_level=$filter_level&f_jenis=$filter_jenis"; ?>
                <li class="page-item <?= ($page <= 1)?'disabled':'' ?>"><a class="page-link" href="?halaman=<?= $page-1 . $qa ?>">Prev</a></li>
                <?php for($i=1;$i<=$total_halaman;$i++): ?>
                    <li class="page-item <?= $page==$i?'active':'' ?>"><a class="page-link" href="?halaman=<?= $i . $qa ?>"><?= $i ?></a></li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $total_halaman)?'disabled':'' ?>"><a class="page-link" href="?halaman=<?= $page+1 . $qa ?>">Next</a></li>
            </ul>
        </nav>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form action="tambah.php?tab=<?= $tab ?>" method="POST" class="modal-content border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Data (<?= ucfirst($tab) ?>)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php if($tab == 'kuis'): ?>
                    <div class="mb-3">
                        <label>Jenis Template Soal</label>
                        <select name="template_soal" class="form-select">
                            <option value="Sinonim dari kata">Sinonim (Persamaan)</option>
                            <option value="Antonim dari kata">Antonim (Lawan Kata)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Kata Kunci</label>
                        <input type="text" name="kata_kuis" class="form-control" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6 mb-2"><input type="text" name="pilihan_0" class="form-control" placeholder="Pilihan A" required></div>
                        <div class="col-6 mb-2"><input type="text" name="pilihan_1" class="form-control" placeholder="Pilihan B" required></div>
                        <div class="col-6"><input type="text" name="pilihan_2" class="form-control" placeholder="Pilihan C" required></div>
                        <div class="col-6"><input type="text" name="pilihan_3" class="form-control" placeholder="Pilihan D" required></div>
                    </div>
                    <div class="mb-3">
                        <label>Kunci Jawaban</label>
                        <select name="jawaban_manusia" class="form-select">
                            <option value="1">Pilihan A</option><option value="2">Pilihan B</option>
                            <option value="3">Pilihan C</option><option value="4">Pilihan D</option>
                        </select>
                    </div>
                <?php else: ?>
                    <div class="mb-3"><label>Kata 1 (Soal)</label><input type="text" name="kata_soal" class="form-control" required></div>
                    <div class="mb-3"><label>Kata 2 (Jawaban)</label><input type="text" name="kata_jawaban" class="form-control" required></div>
                    <div class="mb-3">
                        <label>Jenis Hubungan</label>
                        <select name="jenis" class="form-select">
                            <option value="sinonim">Sinonim</option><option value="antonim">Antonim</option>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="mb-3">
                    <label>Level Soal</label>
                    <select name="level" class="form-select">
                        <option value="1">Level 1</option><option value="2">Level 2</option><option value="3">Level 3</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer"><button type="submit" name="submit" class="btn btn-primary w-100">Simpan Data</button></div>
        </form>
    </div>
</div>
<div class="modal fade" id="modalImport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="import_csv.php" method="POST" enctype="multipart/form-data" class="modal-content border-0">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-arrow-up me-2"></i>Import CSV (<?= ucfirst($tab) ?>)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="tab" value="<?= $tab ?>">
                <div class="alert alert-info border-0 shadow-sm small">
                    <strong>Format Kolom CSV:</strong><br>
                    <?php if($tab == 'kuis'): ?>
                        pertanyaan, pilihan_0, pilihan_1, pilihan_2, pilihan_3, jawaban_index, level
                    <?php else: ?>
                        kata_soal, kata_jawaban, jenis, level
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih File .csv</label>
                    <input type="file" name="file_csv" class="form-control" accept=".csv" required>
                    <div class="form-text text-muted">Gunakan pemisah koma (,) pada file CSV Anda.</div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="proses_import" class="btn btn-success px-4">Mulai Import</button>
            </div>
        </form>
    </div>
</div>

<script>
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    // Fungsi buka/tutup
    function toggleSidebar() {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    }

    // Klik tombol hamburger
    menuToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleSidebar();
    });

    // Klik area gelap (overlay) untuk menutup
    overlay.addEventListener('click', function() {
        toggleSidebar();
    });

    // Tutup sidebar jika layar di-resize ke ukuran desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        }
    });

    // Tunggu sampai halaman selesai dimuat
    document.addEventListener('DOMContentLoaded', function() {
        const autoCloseAlert = document.getElementById('alert-auto-close');
        
        if (autoCloseAlert) {
            // Set waktu tunggu (3000ms = 3 detik)
            setTimeout(function() {
                // Tambahkan efek transisi Bootstrap untuk menutup secara halus
                const alert = new bootstrap.Alert(autoCloseAlert);
                alert.close();
            }, 3000); 
        }
    });

    function konfirmasiHapus(tab, id) {
    Swal.fire({
        title: 'Hapus Data?',
        text: "Data yang dihapus tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Arahkan ke file hapus.php jika user klik Ya
            window.location.href = `hapus.php?tab=${tab}&id=${id}`;
        }
    })
}

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>