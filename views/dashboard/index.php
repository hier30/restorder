<h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard</h2>

<!-- Kartu Ringkasan -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Meja Tersedia</h6>
                        <h3><?= $mejaTersedia ?> / <?= $totalMeja ?></h3>
                    </div>
                    <i class="bi bi-grid-3x3 fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Pelanggan</h6>
                        <h3><?= $totalPelanggan ?></h3>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Pesanan Hari Ini</h6>
                        <h3><?= $pesananHariIni ?></h3>
                    </div>
                    <i class="bi bi-cart3 fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Pendapatan Hari Ini</h6>
                        <h3>Rp <?= number_format((float)$pendapatanHariIni, 0, ',', '.') ?></h3>
                    </div>
                    <i class="bi bi-currency-dollar fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pesanan Terbaru -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Pesanan Terbaru</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pesananTerbaru)): ?>
                        <tr><td colspan="6" class="text-center text-muted">Belum ada pesanan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($pesananTerbaru as $p): ?>
                        <tr>
                            <td>#<?= $p['id'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($p['tanggal_pesanan'])) ?></td>
                            <td><?= htmlspecialchars($p['nama_pelanggan']) ?></td>
                            <td>
                                <span class="badge bg-<?= $p['tipe_pesanan'] === 'dine_in' ? 'primary' : 'secondary' ?>">
                                    <?= $p['tipe_pesanan'] === 'dine_in' ? 'Dine In' : 'Take Away' ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $badgeClass = match($p['status_pesanan']) {
                                    'pending'    => 'bg-warning text-dark',
                                    'diproses'   => 'bg-info',
                                    'selesai'    => 'bg-success',
                                    'dibatalkan' => 'bg-danger',
                                    default      => 'bg-secondary',
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= ucfirst($p['status_pesanan']) ?></span>
                            </td>
                            <td>Rp <?= number_format((float)$p['total'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
