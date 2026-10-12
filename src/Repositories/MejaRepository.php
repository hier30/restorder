<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Meja;
use PDO;
use PDOException;

/**
 * Repository untuk operasi CRUD tabel meja.
 *
 * Semua query menggunakan PDO prepared statement (bukan string concatenation)
 * untuk mencegah SQL injection.
 *
 * @package App\Repositories
 */
class MejaRepository
{
    /** @var PDO Instance koneksi database */
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Ambil semua data meja.
     *
     * @return Meja[] Array of Meja objects
     * @throws PDOException Jika query gagal
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM meja ORDER BY nomor_meja ASC");
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $result[] = $this->mapToModel($row);
        }
        return $result;
    }

    /**
     * Ambil satu meja berdasarkan ID.
     *
     * @param int $id ID meja
     * @return Meja|null Null jika tidak ditemukan
     * @throws PDOException Jika query gagal
     */
    public function findById(int $id): ?Meja
    {
        $stmt = $this->pdo->prepare("SELECT * FROM meja WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->mapToModel($row) : null;
    }

    /**
     * Ambil semua meja yang tersedia (untuk reservasi).
     *
     * @return Meja[] Meja dengan status 'tersedia'
     */
    public function findTersedia(): array
    {
        $stmt = $this->pdo->query(
            "SELECT * FROM meja WHERE status = 'tersedia' ORDER BY nomor_meja ASC"
        );
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $result[] = $this->mapToModel($row);
        }
        return $result;
    }

    /**
     * Simpan meja baru ke database.
     *
     * @param Meja $meja Object meja yang akan disimpan
     * @return int ID meja yang baru dibuat
     * @throws PDOException Jika insert gagal
     */
    public function create(Meja $meja): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO meja (nomor_meja, kapasitas, status)
             VALUES (:nomor_meja, :kapasitas, :status)"
        );
        $stmt->execute([
            'nomor_meja' => $meja->getNomorMeja(),
            'kapasitas'  => $meja->getKapasitas(),
            'status'     => $meja->getStatus(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Update data meja.
     *
     * @param Meja $meja Object meja dengan data terbaru
     * @return bool true jika berhasil
     * @throws PDOException Jika update gagal
     */
    public function update(Meja $meja): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE meja SET nomor_meja = :nomor_meja, kapasitas = :kapasitas, status = :status
             WHERE id = :id"
        );
        return $stmt->execute([
            'nomor_meja' => $meja->getNomorMeja(),
            'kapasitas'  => $meja->getKapasitas(),
            'status'     => $meja->getStatus(),
            'id'         => $meja->getId(),
        ]);
    }

    /**
     * Update status meja saja.
     *
     * @param int    $id     ID meja
     * @param string $status Status baru
     * @return bool true jika berhasil
     */
    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->pdo->prepare("UPDATE meja SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    /**
     * Hapus meja berdasarkan ID.
     *
     * @param int $id ID meja
     * @return bool true jika berhasil
     * @throws PDOException Jika ada pesanan yang merujuk ke meja ini
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM meja WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Mapping array hasil query ke object Meja.
     *
     * @param array $row Baris hasil query
     * @return Meja
     */
    private function mapToModel(array $row): Meja
    {
        return new Meja(
            nomorMeja: $row['nomor_meja'],
            kapasitas: (int) $row['kapasitas'],
            status: $row['status'],
            id: (int) $row['id']
        );
    }
}
