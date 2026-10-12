<h2 class="mb-4">
    <i class="bi bi-book"></i>
    <?= $menu === null ? 'Tambah Menu Baru' : 'Edit Menu' ?>
</h2>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= $formAction ?>">
            <div class="mb-3">
                <label for="nama_menu" class="form-label">Nama Menu <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nama_menu" name="nama_menu"
                       value="<?= htmlspecialchars($menu ? $menu->getNamaMenu() : '') ?>"
                       required maxlength="100">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select class="form-select" id="kategori" name="kategori" required>
                        <?php foreach (['makanan', 'minuman', 'dessert'] as $k): ?>
                            <option value="<?= $k ?>" <?= ($menu && $menu->getKategori() === $k) ? 'selected' : '' ?>>
                                <?= ucfirst($k) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="harga" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="harga" name="harga"
                           value="<?= $menu ? (int) $menu->getHarga() : '' ?>"
                           min="0" step="500" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="2"><?= htmlspecialchars($menu ? ($menu->getDeskripsi() ?? '') : '') ?></textarea>
            </div>
            <div class="mb-3 form-check">
                <input type="hidden" name="tersedia" value="0">
                <input type="checkbox" class="form-check-input" id="tersedia" name="tersedia" value="1"
                       <?= ($menu === null || $menu->isTersedia()) ? 'checked' : '' ?>>
                <label class="form-check-label" for="tersedia">Menu tersedia</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <a href="?page=menu" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
