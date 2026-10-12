<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people"></i> Data Pelanggan</h2>
    <a href="?page=pelanggan&action=create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Pelanggan
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pelangganList)): ?>
                        <tr><td colspan="6" class="text-center text-muted">Belum ada data pelanggan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($pelangganList as $i => $p): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($p->getNama()) ?></td>
                            <td><?= htmlspecialchars($p->getKontak() ?? '-') ?></td>
                            <td><?= htmlspecialchars($p->getEmail() ?? '-') ?></td>
                            <td><?= htmlspecialchars($p->getAlamat() ?? '-') ?></td>
                            <td>
                                <a href="?page=pelanggan&action=edit&id=<?= $p->getId() ?>"
                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="?page=pelanggan&action=delete" class="d-inline"
                                      onsubmit="return confirm('Yakin hapus pelanggan ini?')">
                                    <input type="hidden" name="id" value="<?= $p->getId() ?>">
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
