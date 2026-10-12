<h2 class="mb-4"><i class="bi bi-cart-plus"></i> Buat Pesanan Baru</h2>

<div class="card">
    <div class="card-body">
        <form method="POST" action="?page=pesanan&action=store" id="formPesanan">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="pelanggan_id" class="form-label">Pelanggan <span class="text-danger">*</span></label>
                    <select class="form-select" id="pelanggan_id" name="pelanggan_id" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        <?php foreach ($pelangganList as $p): ?>
                            <option value="<?= $p->getId() ?>"><?= htmlspecialchars($p->getNama()) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="tipe_pesanan" class="form-label">Tipe Pesanan <span class="text-danger">*</span></label>
                    <select class="form-select" id="tipe_pesanan" name="tipe_pesanan" required
                            onchange="toggleMeja()">
                        <option value="dine_in">Dine In (Makan di Tempat)</option>
                        <option value="take_away">Take Away (Bungkus)</option>
                    </select>
                </div>
            </div>

            <div class="mb-3" id="mejaGroup">
                <label for="meja_id" class="form-label">Pilih Meja <span class="text-danger">*</span></label>
                <select class="form-select" id="meja_id" name="meja_id">
                    <option value="">-- Pilih Meja --</option>
                    <?php foreach ($mejaTersedia as $meja): ?>
                        <option value="<?= $meja->getId() ?>">
                            <?= htmlspecialchars($meja->getNomorMeja()) ?> (<?= $meja->getKapasitas() ?> kursi)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($mejaTersedia)): ?>
                    <small class="text-danger">Tidak ada meja yang tersedia saat ini.</small>
                <?php endif; ?>
            </div>

            <!-- Item Pesanan (dinamis) -->
            <h5 class="mt-4 mb-3">Item Pesanan</h5>
            <div id="itemContainer">
                <div class="row mb-2 item-row">
                    <div class="col-md-6">
                        <select class="form-select" name="menu_id[]" required onchange="updateHarga(this)">
                            <option value="">-- Pilih Menu --</option>
                            <?php foreach ($menuList as $m): ?>
                                <option value="<?= $m->getId() ?>" data-harga="<?= $m->getHarga() ?>">
                                    <?= htmlspecialchars($m->getNamaMenu()) ?> - Rp <?= number_format($m->getHarga(), 0, ',', '.') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control" name="jumlah[]" placeholder="Jumlah"
                               min="1" value="1" required onchange="hitungSubtotal()">
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        <span class="subtotal-display text-muted">Rp 0</span>
                    </div>
                    <div class="col-md-1 d-flex align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusItem(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-sm btn-outline-secondary mb-3" onclick="tambahItem()">
                <i class="bi bi-plus"></i> Tambah Item
            </button>

            <div class="card bg-light mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <span>Subtotal:</span>
                        <span id="subtotalDisplay">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between" id="layananRow">
                        <span>Biaya Layanan (10%):</span>
                        <span id="layananDisplay">Rp 0</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Total:</span>
                        <span id="totalDisplay">Rp 0</span>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="catatan" class="form-label">Catatan</label>
                <textarea class="form-control" id="catatan" name="catatan" rows="2"
                          placeholder="Catatan tambahan (opsional)"></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-circle"></i> Buat Pesanan
                </button>
                <a href="?page=pesanan" class="btn btn-secondary btn-lg">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleMeja() {
    const tipe = document.getElementById('tipe_pesanan').value;
    const mejaGroup = document.getElementById('mejaGroup');
    const mejaSelect = document.getElementById('meja_id');
    const layananRow = document.getElementById('layananRow');

    if (tipe === 'take_away') {
        mejaGroup.style.display = 'none';
        mejaSelect.removeAttribute('required');
        mejaSelect.value = '';
        layananRow.style.display = 'none';
    } else {
        mejaGroup.style.display = 'block';
        mejaSelect.setAttribute('required', 'required');
        layananRow.style.display = 'flex';
    }
    hitungSubtotal();
}

function tambahItem() {
    const container = document.getElementById('itemContainer');
    const firstRow = container.querySelector('.item-row');
    const newRow = firstRow.cloneNode(true);
    newRow.querySelector('select').selectedIndex = 0;
    newRow.querySelector('input').value = 1;
    newRow.querySelector('.subtotal-display').textContent = 'Rp 0';
    container.appendChild(newRow);
}

function hapusItem(btn) {
    const container = document.getElementById('itemContainer');
    if (container.querySelectorAll('.item-row').length > 1) {
        btn.closest('.item-row').remove();
        hitungSubtotal();
    }
}

function updateHarga(select) {
    hitungSubtotal();
}

function hitungSubtotal() {
    const rows = document.querySelectorAll('.item-row');
    let subtotal = 0;

    rows.forEach(row => {
        const select = row.querySelector('select');
        const jumlah = parseInt(row.querySelector('input').value) || 0;
        const option = select.options[select.selectedIndex];
        const harga = parseFloat(option?.dataset?.harga || 0);
        const itemSubtotal = harga * jumlah;

        row.querySelector('.subtotal-display').textContent = 'Rp ' + itemSubtotal.toLocaleString('id-ID');
        subtotal += itemSubtotal;
    });

    const tipe = document.getElementById('tipe_pesanan').value;
    const layanan = tipe === 'dine_in' ? subtotal * 0.10 : 0;
    const total = subtotal + layanan;

    document.getElementById('subtotalDisplay').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    document.getElementById('layananDisplay').textContent = 'Rp ' + layanan.toLocaleString('id-ID');
    document.getElementById('totalDisplay').textContent = 'Rp ' + total.toLocaleString('id-ID');
}
</script>
