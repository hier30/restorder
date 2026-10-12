<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-grid-3x3"></i> Kelola Meja</h2>
    <a href="?page=meja&action=create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Meja
    </a>
</div>

<!-- Peta Meja Visual -->
<div class="row mb-4">
    <?php foreach ($mejaList as $meja): ?>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card h-100 border-<?= match($meja->getStatus()) {
            'tersedia' => 'success',
            'terisi'   => 'danger',
            'dipesan'  => 'warning',
        } ?>">
            <div class="card-body text-center">
                <h4><?= htmlspecialchars($meja->getNomorMeja()) ?></h4>
                <p class="text-muted mb-1"><i class="bi bi-person"></i> <?= $meja->getKapasitas() ?> kursi</p>
                <?php
                $badgeClass = match($meja->getStatus()) {
                    'tersedia' => 'bg-success',
                    'terisi'   => 'bg-danger',
                    'dipesan'  => 'bg-warning text-dark',
                };
                ?>
                <span class="badge <?= $badgeClass ?> mb-2"><?= ucfirst($meja->getStatus()) ?></span>
                <div class="mt-2">
                    <?php if ($meja->getStatus() === 'tersedia'): ?>
                        <a href="?page=meja&action=reservasi&id=<?= $meja->getId() ?>"
                           class="btn btn-sm btn-outline-warning" title="Reservasi">
                            <i class="bi bi-bookmark-plus"></i> Reservasi
                        </a>
                    <?php elseif ($meja->getStatus() !== 'tersedia'): ?>
                        <a href="?page=meja&action=bebaskan&id=<?= $meja->getId() ?>"
                           class="btn btn-sm btn-outline-success" title="Bebaskan">
                            <i class="bi bi-unlock"></i> Bebaskan
                        </a>
                    <?php endif; ?>
                    <a href="?page=meja&action=edit&id=<?= $meja->getId() ?>"
                       class="btn btn-sm btn-outline-primary" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form method="POST" action="?page=meja&action=delete" class="d-inline"
                          onsubmit="return confirm('Yakin hapus meja ini?')">
                        <input type="hidden" name="id" value="<?= $meja->getId() ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if (empty($mejaList)): ?>
        <div class="col-12">
            <p class="text-muted text-center">Belum ada data meja. Klik "Tambah Meja" untuk menambahkan.</p>
        </div>
    <?php endif; ?>
</div>

<div class="d-flex gap-3 text-muted small">
    <span><span class="badge bg-success">&nbsp;&nbsp;</span> Tersedia</span>
    <span><span class="badge bg-warning">&nbsp;&nbsp;</span> Dipesan</span>
    <span><span class="badge bg-danger">&nbsp;&nbsp;</span> Terisi</span>
</div>
