<h2 class="mb-4">
    <i class="bi bi-people"></i>
    <?= $pelanggan === null ? 'Tambah Pelanggan Baru' : 'Edit Pelanggan' ?>
</h2>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= $formAction ?>">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nama" name="nama"
                       value="<?= htmlspecialchars($pelanggan ? $pelanggan->getNama() : '') ?>"
                       required maxlength="100">
            </div>
            <div class="mb-3">
                <label for="no_telepon" class="form-label">Nomor Telepon</label>
                <input type="text" class="form-control" id="no_telepon" name="no_telepon"
                       value="<?= htmlspecialchars($pelanggan ? ($pelanggan->getKontak() ?? '') : '') ?>"
                       placeholder="081234567890">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                       value="<?= htmlspecialchars($pelanggan ? ($pelanggan->getEmail() ?? '') : '') ?>"
                       placeholder="contoh@email.com">
            </div>
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat" rows="2"><?= htmlspecialchars($pelanggan ? ($pelanggan->getAlamat() ?? '') : '') ?></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <a href="?page=pelanggan" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
