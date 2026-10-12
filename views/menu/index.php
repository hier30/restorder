<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-book"></i> Daftar Menu</h2>
    <a href="?page=menu&action=create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Menu
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Menu</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($menuList)): ?>
                        <tr><td colspan="7" class="text-center text-muted">Belum ada data menu.</td></tr>
                    <?php else: ?>
                        <?php foreach ($menuList as $i => $m): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($m->getNamaMenu()) ?></td>
                            <td>
                                <span class="badge bg-<?= match($m->getKategori()) {
                                    'makanan' => 'primary',
                                    'minuman' => 'info',
                                    'dessert' => 'warning text-dark',
                                } ?>">
                                    <?= ucfirst($m->getKategori()) ?>
                                </span>
                            </td>
                            <td>Rp <?= number_format($m->getHarga(), 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($m->getDeskripsi() ?? '-') ?></td>
                            <td>
                                <?php if ($m->isTersedia()): ?>
                                    <span class="badge bg-success">Tersedia</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Habis</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?page=menu&action=edit&id=<?= $m->getId() ?>"
                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="?page=menu&action=delete" class="d-inline"
                                      onsubmit="return confirm('Yakin hapus menu ini?')">
                                    <input type="hidden" name="id" value="<?= $m->getId() ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
