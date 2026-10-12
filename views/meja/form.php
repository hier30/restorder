<h2 class="mb-4">
    <i class="bi bi-grid-3x3"></i>
    <?= $meja === null ? 'Tambah Meja Baru' : 'Edit Meja' ?>
</h2>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= $formAction ?>">
            <div class="mb-3">
                <label for="nomor_meja" class="form-label">Nomor Meja <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nomor_meja" name="nomor_meja"
                       value="<?= htmlspecialchars($meja ? $meja->getNomorMeja() : '') ?>"
                       placeholder="Contoh: M01" required>
            </div>
            <div class="mb-3">
                <label for="kapasitas" class="form-label">Kapasitas (kursi) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="kapasitas" name="kapasitas"
                       value="<?= $meja ? $meja->getKapasitas() : 4 ?>"
                       min="1" max="20" required>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <?php foreach (['tersedia', 'terisi', 'dipesan'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($meja && $meja->getStatus() === $s) ? 'selected' : '' ?>>
                            <?= ucfirst($s) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <a href="?page=meja" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
