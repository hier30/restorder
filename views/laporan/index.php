<h2 class="mb-4"><i class="bi bi-file-earmark-bar-graph"></i> Laporan Pemesanan</h2>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="laporan">
            <div class="col-md-3">
                <label for="tanggal_mulai" class="form-label">Dari Tanggal</label>
                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai"
                       value="<?= htmlspecialchars($filterMulai ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label for="tanggal_akhir" class="form-label">Sampai Tanggal</label>
                <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir"
                       value="<?= htmlspecialchars($filterAkhir ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label for="pelanggan_id" class="form-label">Pelanggan</label>
                <select class="form-select" id="pelanggan_id" name="pelanggan_id">
                    <option value="">-- Semua Pelanggan --</option>
                    <?php foreach ($pelangganList as $p): ?>
                        <option value="<?= $p->getId() ?>"
                                <?= ($filterPelanggan ?? 0) == $p->getId() ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p->getNama()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Ringkasan -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card text-white bg-info">
            <div class="card-body text-center">
                <h6>Jumlah Pesanan</h6>
                <h3><?= $ringkasan['jumlah_pesanan'] ?? 0 ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card text-white bg-success">
            <div class="card-body text-center">
                <h6>Total Pendapatan</h6>
                <h3>Rp <?= number_format((float)($ringkasan['total_pendapatan'] ?? 0), 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Laporan -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Data Pesanan</strong>
        <a href="?page=laporan&action=exportPdf&tanggal_mulai=<?= urlencode($filterMulai ?? '') ?>&tanggal_akhir=<?= urlencode($filterAkhir ?? '') ?>&pelanggan_id=<?= $filterPelanggan ?? '' ?>"
           class="btn btn-danger btn-sm" target="_blank">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Tipe</th>
                        <th>Meja</th>
                        <th>Status</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pesananList)): ?>
                        <tr><td colspan="7" class="text-center text-muted">Tidak ada data pesanan untuk filter ini.</td></tr>
                    <?php else: ?>
                        <?php foreach ($pesananList as $i => $p): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($p['tanggal_pesanan'])) ?></td>
                            <td><?= htmlspecialchars($p['nama_pelanggan']) ?></td>
                            <td>
                                <span class="badge bg-<?= $p['tipe_pesanan'] === 'dine_in' ? 'primary' : 'secondary' ?>">
                                    <?= $p['tipe_pesanan'] === 'dine_in' ? 'Dine In' : 'Take Away' ?>
                                </span>
                            </td>
                            <td><?= $p['nomor_meja'] ?? '-' ?></td>
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
                            <td class="text-end">Rp <?= number_format((float)$p['total'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
