<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use PDOException;

/**
 * Repository untuk operasi CRUD tabel pesanan dan detail_pesanan.
 *
 * Menangani transaksi database (begin/commit/rollback) untuk menjaga
 * konsistensi data antara tabel pesanan dan detail_pesanan.
 *
 * @package App\Repositories
 */
class PesananRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Ambil semua pesanan dengan data pelanggan & meja (JOIN).
     *
     * @return array Hasil query sebagai array asosiatif
     */
    public function findAll(): array
    {
        $sql = "SELECT p.*, pel.nama AS nama_pelanggan, m.nomor_meja
                FROM pesanan p
                LEFT JOIN pelanggan pel ON p.pelanggan_id = pel.id
                LEFT JOIN meja m ON p.meja_id = m.id
                ORDER BY p.tanggal_pesanan DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Cari pesanan berdasarkan ID, termasuk detail item-nya.
     *
     * @param int $id ID pesanan
     * @return array|null Null jika tidak ditemukan
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, pel.nama AS nama_pelanggan, m.nomor_meja
             FROM pesanan p
             LEFT JOIN pelanggan pel ON p.pelanggan_id = pel.id
             LEFT JOIN meja m ON p.meja_id = m.id
             WHERE p.id = :id"
        );
        $stmt->execute(['id' => $id]);
        $pesanan = $stmt->fetch();

        if (!$pesanan) {
            return null;
        }

        $pesanan['items'] = $this->findDetailByPesananId($id);
        return $pesanan;
    }

    /**
     * Ambil detail item pesanan.
     *
     * @param int $pesananId ID pesanan
     * @return array Daftar item pesanan
     */
    public function findDetailByPesananId(int $pesananId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT dp.*, mn.nama_menu
             FROM detail_pesanan dp
             LEFT JOIN menu mn ON dp.menu_id = mn.id
             WHERE dp.pesanan_id = :pesanan_id"
        );
        $stmt->execute(['pesanan_id' => $pesananId]);
        return $stmt->fetchAll();
    }

    /**
     * Simpan pesanan baru beserta detail item-nya (dalam satu transaksi).
     *
     * @param array $pesananData Data header pesanan
     * @param array $items       Array of ['menu_id', 'jumlah', 'harga_saat_pesan', 'subtotal']
     * @return int ID pesanan yang baru dibuat
     * @throws PDOException Jika transaksi gagal (otomatis rollback)
     */
    public function create(array $pesananData, array $items): int
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO pesanan (pelanggan_id, meja_id, tipe_pesanan, status_pesanan,
                        biaya_layanan, total, tanggal_pesanan, catatan)
                 VALUES (:pelanggan_id, :meja_id, :tipe_pesanan, :status_pesanan,
                        :biaya_layanan, :total, :tanggal_pesanan, :catatan)"
            );
            $stmt->execute([
                'pelanggan_id'   => $pesananData['pelanggan_id'],
                'meja_id'        => $pesananData['meja_id'] ?? null,
                'tipe_pesanan'   => $pesananData['tipe_pesanan'],
                'status_pesanan' => $pesananData['status_pesanan'] ?? 'pending',
                'biaya_layanan'  => $pesananData['biaya_layanan'] ?? 0,
                'total'          => $pesananData['total'] ?? 0,
                'tanggal_pesanan' => $pesananData['tanggal_pesanan'] ?? date('Y-m-d H:i:s'),
                'catatan'        => $pesananData['catatan'] ?? null,
            ]);

            $pesananId = (int) $this->pdo->lastInsertId();

            $stmtDetail = $this->pdo->prepare(
                "INSERT INTO detail_pesanan (pesanan_id, menu_id, jumlah, harga_saat_pesan, subtotal)
                 VALUES (:pesanan_id, :menu_id, :jumlah, :harga_saat_pesan, :subtotal)"
            );

            foreach ($items as $item) {
                $stmtDetail->execute([
                    'pesanan_id'      => $pesananId,
                    'menu_id'         => $item['menu_id'],
                    'jumlah'          => $item['jumlah'],
                    'harga_saat_pesan' => $item['harga_saat_pesan'],
                    'subtotal'        => $item['subtotal'],
                ]);
            }

            $this->pdo->commit();
            return $pesananId;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Update status pesanan.
     *
     * @param int    $id     ID pesanan
     * @param string $status Status baru
     * @return bool
     */
    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE pesanan SET status_pesanan = :status WHERE id = :id"
        );
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    /**
     * Hapus pesanan beserta detail-nya (CASCADE di FK menangani detail).
     *
     * @param int $id ID pesanan
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM pesanan WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Filter pesanan untuk laporan berdasarkan rentang tanggal dan/atau pelanggan.
     *
     * Mendemonstrasikan PEMROGRAMAN TERSTRUKTUR: membangun query secara dinamis
     * menggunakan percabangan (if) untuk menambahkan kondisi WHERE.
     *
     * @param string|null $tanggalMulai Format Y-m-d
     * @param string|null $tanggalAkhir Format Y-m-d
     * @param int|null    $pelangganId  Filter per pelanggan
     * @return array Hasil query untuk laporan
     */
    public function findForLaporan(
        ?string $tanggalMulai = null,
        ?string $tanggalAkhir = null,
        ?int $pelangganId = null
    ): array {
        $sql = "SELECT p.*, pel.nama AS nama_pelanggan, m.nomor_meja
                FROM pesanan p
                LEFT JOIN pelanggan pel ON p.pelanggan_id = pel.id
                LEFT JOIN meja m ON p.meja_id = m.id
                WHERE 1=1";

        $params = [];

        if ($tanggalMulai !== null && $tanggalMulai !== '') {
            $sql .= " AND DATE(p.tanggal_pesanan) >= :tanggal_mulai";
            $params['tanggal_mulai'] = $tanggalMulai;
        }

        if ($tanggalAkhir !== null && $tanggalAkhir !== '') {
            $sql .= " AND DATE(p.tanggal_pesanan) <= :tanggal_akhir";
            $params['tanggal_akhir'] = $tanggalAkhir;
        }

        if ($pelangganId !== null && $pelangganId > 0) {
            $sql .= " AND p.pelanggan_id = :pelanggan_id";
            $params['pelanggan_id'] = $pelangganId;
        }

        $sql .= " ORDER BY p.tanggal_pesanan DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Hitung ringkasan laporan (total pesanan, total pendapatan).
     *
     * @param string|null $tanggalMulai
     * @param string|null $tanggalAkhir
     * @param int|null    $pelangganId
     * @return array ['jumlah_pesanan' => int, 'total_pendapatan' => float]
     */
    public function hitungRingkasanLaporan(
        ?string $tanggalMulai = null,
        ?string $tanggalAkhir = null,
        ?int $pelangganId = null
    ): array {
        $sql = "SELECT COUNT(*) AS jumlah_pesanan, COALESCE(SUM(total), 0) AS total_pendapatan
                FROM pesanan WHERE 1=1";

        $params = [];

        if ($tanggalMulai !== null && $tanggalMulai !== '') {
            $sql .= " AND DATE(tanggal_pesanan) >= :tanggal_mulai";
            $params['tanggal_mulai'] = $tanggalMulai;
        }
        if ($tanggalAkhir !== null && $tanggalAkhir !== '') {
            $sql .= " AND DATE(tanggal_pesanan) <= :tanggal_akhir";
            $params['tanggal_akhir'] = $tanggalAkhir;
        }
        if ($pelangganId !== null && $pelangganId > 0) {
            $sql .= " AND pelanggan_id = :pelanggan_id";
            $params['pelanggan_id'] = $pelangganId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
}
