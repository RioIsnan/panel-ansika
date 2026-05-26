<div class="sidebar shadow" id="sidebar">
    <div class="p-4 text-center border-bottom border-secondary border-opacity-10 mb-3">
        <h3 class="text-info fw-bold mb-0">ANSIKA</h3>
        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Admin Panel</small>
    </div>
    <div class="mt-2">
        <a href="dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2 me-3"></i> Dashboard
        </a>
        <a href="index.php?tab=kuis" class="nav-link <?= (isset($_GET['tab']) && $_GET['tab'] == 'kuis') ? 'active' : '' ?>">
            <i class="bi bi-pencil-square me-3"></i> Kelola Kuis
        </a>
        <a href="index.php?tab=cocok_kata" class="nav-link <?= (isset($_GET['tab']) && $_GET['tab'] == 'cocok_kata') ? 'active' : '' ?>">
            <i class="bi bi-intersect me-3"></i> Cocok Kata
        </a>
        
        <div style="position: absolute; bottom: 50px; width: 100%;">
            <hr class="mx-4 text-secondary opacity-25">
            <a href="logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-left me-3"></i> Keluar</a>
        </div>
    </div>
</div>