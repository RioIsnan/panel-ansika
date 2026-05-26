<?php 
include "cek_login.php"; 
include "koneksi.php"; 

// --- AMBIL DATA STATISTIK KUIS ---
// Menghitung total kuis
$qTotalKuis = $conn->query("SELECT COUNT(*) as total FROM tabel_kuis");
$totalKuis  = $qTotalKuis->fetch_assoc()['total'];

// Menghitung kuis sinonim (Asumsi: kolom pertanyaan diawali kata 'Sinonim')
// Sesuaikan query ini dengan struktur filter di index.php Anda
$qKuisSinonim = $conn->query("SELECT COUNT(*) as total FROM tabel_kuis WHERE pertanyaan LIKE 'Sinonim%'");
$kuisSinonim  = $qKuisSinonim->fetch_assoc()['total'];

$qKuisAntonim = $conn->query("SELECT COUNT(*) as total FROM tabel_kuis WHERE pertanyaan LIKE 'Antonim%'");
$kuisAntonim  = $qKuisAntonim->fetch_assoc()['total'];


// --- AMBIL DATA STATISTIK COCOK KATA ---
$qTotalCocok = $conn->query("SELECT COUNT(*) as total FROM tabel_cocok_kata");
$totalCocok  = $qTotalCocok->fetch_assoc()['total'];

$qCocokSinonim = $conn->query("SELECT COUNT(*) as total FROM tabel_cocok_kata WHERE jenis = 'Sinonim'");
$cocokSinonim  = $qCocokSinonim->fetch_assoc()['total'];

$qCocokAntonim = $conn->query("SELECT COUNT(*) as total FROM tabel_cocok_kata WHERE jenis = 'Antonim'");
$cocokAntonim  = $qCocokAntonim->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ANSIKA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="p-4 text-center border-bottom border-secondary mb-3">
        <h3 class="text-info fw-bold mb-0">ANSIKA</h3>
    </div>
    <a href="dashboard.php" class="nav-link active"><i class="bi bi-grid-1x2 me-3"></i> Dashboard</a>
    <a href="index.php?tab=kuis" class="nav-link"><i class="bi bi-pencil-square me-3"></i> Kelola Kuis</a>
    <a href="index.php?tab=cocok_kata" class="nav-link"><i class="bi bi-intersect me-3"></i> Cocok Kata</a>
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

    <h2 class="fw-bold mb-4">Ringkasan Data</h2>
    
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card stat-card p-4 border-0 shadow-sm">
                <h6 class="text-muted fw-bold mb-4">STATISTIK KUIS</h6>
                <div class="row text-center">
                    <div class="col-4">
                        <h3 class="fw-bold"><?= $totalKuis ?></h3>
                        <small class="text-muted small">Total</small>
                    </div>
                    <div class="col-4 border-start text-primary">
                        <h3 class="fw-bold"><?= $kuisSinonim ?></h3>
                        <small class="small">Sinonim</small>
                    </div>
                    <div class="col-4 border-start text-danger">
                        <h3 class="fw-bold"><?= $kuisAntonim ?></h3>
                        <small class="small">Antonim</small>
                    </div>
                </div>
                <a href="index.php?tab=kuis" class="btn btn-light btn-sm mt-4 w-100 rounded-pill">Detail Kuis</a>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card stat-card p-4 border-0 shadow-sm">
                <h6 class="text-muted fw-bold mb-4">STATISTIK COCOK KATA</h6>
                <div class="row text-center">
                    <div class="col-4">
                        <h3 class="fw-bold"><?= $totalCocok ?></h3>
                        <small class="text-muted small">Total</small>
                    </div>
                    <div class="col-4 border-start text-primary">
                        <h3 class="fw-bold"><?= $cocokSinonim ?></h3>
                        <small class="small">Sinonim</small>
                    </div>
                    <div class="col-4 border-start text-danger">
                        <h3 class="fw-bold"><?= $cocokAntonim ?></h3>
                        <small class="small">Antonim</small>
                    </div>
                </div>
                <a href="index.php?tab=cocok_kata" class="btn btn-light btn-sm mt-4 w-100 rounded-pill">Detail Cocok Kata</a>
            </div>
        </div>
    </div>
</div>

<script>
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function toggleSidebar() {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    }

    menuToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleSidebar();
    });

    overlay.addEventListener('click', toggleSidebar);

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        }
    });
</script>
</body>
</html>