<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Sistem Pemesanan Restoran') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        .sidebar { min-height: 100vh; background-color: #2c3e50; }
        .sidebar .nav-link { color: #ecf0f1; padding: 12px 20px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #34495e; color: #fff; }
        .sidebar .nav-link i { margin-right: 8px; }
        .sidebar-header { color: #fff; padding: 20px; font-size: 1.1rem; font-weight: bold; border-bottom: 1px solid #34495e; }
        .content { padding: 20px; }

        /* Transisi halus antar halaman (View Transitions API, Chrome/Edge 126+) */
        @view-transition { navigation: auto; }
        .sidebar { view-transition-name: sidebar; }
        main.content { view-transition-name: content; }
        ::view-transition-old(content) { animation: fadeOut .15s ease both; }
        ::view-transition-new(content) { animation: fadeIn .25s ease both; }
        @keyframes fadeIn  { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }

        /* Fallback untuk browser tanpa View Transitions (Firefox/Safari) */
        @supports not (view-transition-name: none) {
            main.content { animation: fadeIn .25s ease both; }
        }
        .sidebar .nav-link { transition: background-color .2s ease; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation -->
            <nav class="col-md-2 d-none d-md-block sidebar">
                <div class="sidebar-header">
                    <i class="bi bi-shop"></i> RestOrder
                </div>
                <ul class="nav flex-column mt-2">
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>"
                           href="?page=dashboard">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'meja' ? 'active' : '' ?>"
                           href="?page=meja">
                            <i class="bi bi-grid-3x3"></i> Kelola Meja
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'pelanggan' ? 'active' : '' ?>"
                           href="?page=pelanggan">
                            <i class="bi bi-people"></i> Pelanggan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'menu' ? 'active' : '' ?>"
                           href="?page=menu">
                            <i class="bi bi-book"></i> Menu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'pesanan' ? 'active' : '' ?>"
                           href="?page=pesanan">
                            <i class="bi bi-cart3"></i> Pesanan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'laporan' ? 'active' : '' ?>"
                           href="?page=laporan">
                            <i class="bi bi-file-earmark-bar-graph"></i> Laporan
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto content">
                <!-- Mobile navbar -->
                <nav class="navbar navbar-expand-md d-md-none bg-dark mb-3">
                    <div class="container-fluid">
                        <a class="navbar-brand text-white" href="?page=dashboard">RestOrder</a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNav">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="mobileNav">
                            <ul class="navbar-nav">
                                <li class="nav-item"><a class="nav-link text-white" href="?page=dashboard">Dashboard</a></li>
                                <li class="nav-item"><a class="nav-link text-white" href="?page=meja">Meja</a></li>
                                <li class="nav-item"><a class="nav-link text-white" href="?page=pelanggan">Pelanggan</a></li>
                                <li class="nav-item"><a class="nav-link text-white" href="?page=menu">Menu</a></li>
                                <li class="nav-item"><a class="nav-link text-white" href="?page=pesanan">Pesanan</a></li>
                                <li class="nav-item"><a class="nav-link text-white" href="?page=laporan">Laporan</a></li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <?php if (!empty($flashMessage)): ?>
                    <div class="alert alert-<?= htmlspecialchars($flashType ?? 'info') ?> alert-dismissible fade show">
                        <?= htmlspecialchars($flashMessage) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?= $content ?? '' ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
