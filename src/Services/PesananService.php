<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PesananDineIn;
use App\Models\PesananTakeAway;
use App\Repositories\MenuRepository;
use App\Repositories\MejaRepository;
use App\Repositories\PesananRepository;
use InvalidArgumentException;
use PDOException;

/**
 * Service untuk logika bisnis pesanan.
 *
 * Mendemonstrasikan PEMROGRAMAN TERSTRUKTUR: kalkulasi total pesanan,
 * percabangan (dine-in vs take-away), loop untuk menghitung subtotal item,
 * dan validasi data — semuanya dipisahkan dari controller dan repository.
 *
 * @package App\Services
 */
class PesananService
{
    private PesananRepository $pesananRepo;
    private MenuRepository $menuRepo;
    private MejaRepository $mejaRepo;
    private ValidasiService $validasi;

    public function __construct(
        PesananRepository $pesananRepo,
        MenuRepository $menuRepo,
        MejaRepository $mejaRepo,
        ValidasiService $validasi
    ) {
        $this->pesananRepo = $pesananRepo;
        $this->menuRepo    = $menuRepo;
        $this->mejaRepo    = $mejaRepo;
        $this->validasi    = $validasi;
    }

    /**
     * Proses pembuatan pesanan baru.
     *
     * Langkah-langkah (pemrograman terstruktur):
     * 1. Validasi input
     * 2. Ambil harga menu dari DB (snapshot harga saat pesan)
     * 3. Hitung subtotal per item (loop)
     * 4. Buat object PesananDineIn atau PesananTakeAway (percabangan)
     * 5. Hitung total menggunakan polymorphism (hitungTotal())
     * 6. Simpan ke database (repository)
     * 7. Update status meja jika dine-in
     *
     * @param array $data Data pesanan dari form
     * @return int ID pesanan yang baru dibuat
     * @throws InvalidArgumentException Jika validasi gagal
     * @throws PDOException Jika operasi database gagal
     */
    public function buatPesanan(array $data): int
    {
        // 1. Validasi input
        $validated = $this->validasi->validasiPesanan($data);

        // 2-3. Ambil harga menu & hitung subtotal per item
        $itemsToSave = [];
        foreach ($validated['items'] as $item) {
            $menu = $this->menuRepo->findById((int) $item['menu_id']);
            if ($menu === null) {
                throw new InvalidArgumentException(
                    "Menu dengan ID {$item['menu_id']} tidak ditemukan."
                );
            }
            if (!$menu->isTersedia()) {
                throw new InvalidArgumentException(
                    "Menu '{$menu->getNamaMenu()}' sedang tidak tersedia."
                );
            }

            $jumlah = (int) $item['jumlah'];
            $harga  = $menu->getHarga();

            $itemsToSave[] = [
                'menu_id'         => $menu->getId(),
                'jumlah'          => $jumlah,
                'harga_saat_pesan' => $harga,
                'subtotal'        => $jumlah * $harga,
            ];
        }

        // 4. Buat object pesanan sesuai tipe (percabangan OOP)
        if ($validated['tipe_pesanan'] === 'dine_in') {
            $pesanan = new PesananDineIn(
                $validated['pelanggan_id'],
                $validated['meja_id'],
                $validated['catatan']
            );
        } else {
            $pesanan = new PesananTakeAway(
                $validated['pelanggan_id'],
                $validated['catatan']
            );
        }

        // Tambahkan item ke object pesanan
        foreach ($itemsToSave as $item) {
            $pesanan->tambahItem($item);
        }

        // 5. Hitung total (polymorphism — dine-in vs take-away)
        $total = $pesanan->hitungTotal();
        $biayaLayanan = 0.0;
        if ($pesanan instanceof PesananDineIn) {
            $biayaLayanan = $pesanan->getBiayaLayanan();
        }

        // 6. Simpan ke database
        $pesananData = [
            'pelanggan_id'    => $validated['pelanggan_id'],
            'meja_id'         => $validated['meja_id'],
            'tipe_pesanan'    => $pesanan->getTipePesanan(),
            'status_pesanan'  => 'pending',
            'biaya_layanan'   => $biayaLayanan,
            'total'           => $total,
            'tanggal_pesanan' => date('Y-m-d H:i:s'),
            'catatan'         => $validated['catatan'],
        ];

        $pesananId = $this->pesananRepo->create($pesananData, $itemsToSave);

        // 7. Update status meja menjadi 'terisi' jika dine-in
        if ($validated['meja_id'] !== null) {
            $this->mejaRepo->updateStatus($validated['meja_id'], 'terisi');
        }

        return $pesananId;
    }

    /**
     * Selesaikan pesanan (update status dan bebaskan meja jika dine-in).
     *
     * @param int $id ID pesanan
     * @return bool
     */
    public function selesaikanPesanan(int $id): bool
    {
        $pesanan = $this->pesananRepo->findById($id);
        if ($pesanan === null) {
            throw new InvalidArgumentException("Pesanan dengan ID {$id} tidak ditemukan.");
        }

        $this->pesananRepo->updateStatus($id, 'selesai');

        // Bebaskan meja jika dine-in
        if ($pesanan['meja_id'] !== null) {
            $this->mejaRepo->updateStatus((int) $pesanan['meja_id'], 'tersedia');
        }

        return true;
    }

    /**
     * Batalkan pesanan.
     *
     * @param int $id ID pesanan
     * @return bool
     */
    public function batalkanPesanan(int $id): bool
    {
        $pesanan = $this->pesananRepo->findById($id);
        if ($pesanan === null) {
            throw new InvalidArgumentException("Pesanan dengan ID {$id} tidak ditemukan.");
        }

        $this->pesananRepo->updateStatus($id, 'dibatalkan');

        if ($pesanan['meja_id'] !== null) {
            $this->mejaRepo->updateStatus((int) $pesanan['meja_id'], 'tersedia');
        }

        return true;
    }
}
