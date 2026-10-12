<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-cart3"></i> Daftar Pesanan</h2>
    <a href="?page=pesanan&action=create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Buat Pesanan
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Tipe</th>
                        <th>Meja</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pesananList)): ?>
                        <tr><td colspan="8" class="text-center text-muted">Belum ada pesanan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($pesananList as $p): ?>
                        <tr>
                            <td>#<?= $p['id'] ?></td>
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
                            <td>Rp <?= number_format((float)$p['total'], 0, ',', '.') ?></td>
                            <td>
                                <a href="?page=pesanan&action=detail&id=<?= $p['id'] ?>"
                                   class="btn btn-sm btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php if (in_array($p['status_pesanan'], ['pending', 'diproses'])): ?>
                                    <a href="?page=pesanan&action=selesai&id=<?= $p['id'] ?>"
                                       class="btn btn-sm btn-outline-success" title="Selesaikan"
                                       onclick="return confirm('Tandai pesanan ini selesai?')">
                                        <i class="bi bi-check-circle"></i>
                                    </a>
                                    <a href="?page=pesanan&action=batal&id=<?= $p['id'] ?>"
                                       class="btn btn-sm btn-outline-warning" title="Batalkan"
                                       onclick="return confirm('Batalkan pesanan ini?')">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="?page=pesanan&action=delete&id=<?= $p['id'] ?>"
                                   class="btn btn-sm btn-outline-danger" title="Hapus"
                                   onclick="return confirm('Yakin hapus pesanan ini?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
