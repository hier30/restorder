<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-receipt"></i> Detail Pesanan #<?= $pesanan['id'] ?></h2>
    <a href="?page=pesanan" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header"><strong>Informasi Pesanan</strong></div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%">Tanggal</td>
                        <td><?= date('d/m/Y H:i', strtotime($pesanan['tanggal_pesanan'])) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Pelanggan</td>
                        <td><?= htmlspecialchars($pesanan['nama_pelanggan']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tipe</td>
                        <td>
                            <span class="badge bg-<?= $pesanan['tipe_pesanan'] === 'dine_in' ? 'primary' : 'secondary' ?>">
                                <?= $pesanan['tipe_pesanan'] === 'dine_in' ? 'Dine In' : 'Take Away' ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Meja</td>
                        <td><?= $pesanan['nomor_meja'] ?? '-' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            <?php
                            $badgeClass = match($pesanan['status_pesanan']) {
                                'pending'    => 'bg-warning text-dark',
                                'diproses'   => 'bg-info',
                                'selesai'    => 'bg-success',
                                'dibatalkan' => 'bg-danger',
                                default      => 'bg-secondary',
                            };
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= ucfirst($pesanan['status_pesanan']) ?></span>
                        </td>
                    </tr>
                    <?php if ($pesanan['catatan']): ?>
                    <tr>
                        <td class="text-muted">Catatan</td>
                        <td><?= htmlspecialchars($pesanan['catatan']) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header"><strong>Rincian Biaya</strong></div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Harga</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $subtotalItems = 0;
                        foreach ($pesanan['items'] as $item):
                            $subtotalItems += (float) $item['subtotal'];
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($item['nama_menu']) ?></td>
                            <td class="text-center"><?= $item['jumlah'] ?></td>
                            <td class="text-end">Rp <?= number_format((float)$item['harga_saat_pesan'], 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format((float)$item['subtotal'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end">Subtotal</td>
                            <td class="text-end">Rp <?= number_format($subtotalItems, 0, ',', '.') ?></td>
                        </tr>
                        <?php if ((float) $pesanan['biaya_layanan'] > 0): ?>
                        <tr>
                            <td colspan="3" class="text-end">Biaya Layanan</td>
                            <td class="text-end">Rp <?= number_format((float)$pesanan['biaya_layanan'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr class="fw-bold fs-5">
                            <td colspan="3" class="text-end">Total</td>
                            <td class="text-end">Rp <?= number_format((float)$pesanan['total'], 0, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
