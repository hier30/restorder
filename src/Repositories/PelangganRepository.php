<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Pelanggan;
use PDO;
use PDOException;

/**
 * Repository untuk operasi CRUD tabel pelanggan.
 *
 * @package App\Repositories
 */
class PelangganRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Ambil semua pelanggan.
     *
     * @return Pelanggan[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM pelanggan ORDER BY nama ASC");
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $result[] = $this->mapToModel($row);
        }
        return $result;
    }

    /**
     * Cari pelanggan berdasarkan ID.
     *
     * @param int $id
     * @return Pelanggan|null
     */
    public function findById(int $id): ?Pelanggan
    {
        $stmt = $this->pdo->prepare("SELECT * FROM pelanggan WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->mapToModel($row) : null;
    }

    /**
     * Cari pelanggan berdasarkan nama (LIKE search).
     *
     * @param string $nama Kata kunci pencarian
     * @return Pelanggan[]
     */
    public function findByNama(string $nama): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM pelanggan WHERE nama LIKE :nama ORDER BY nama ASC"
        );
        $stmt->execute(['nama' => "%{$nama}%"]);
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $result[] = $this->mapToModel($row);
        }
        return $result;
    }

    /**
     * Simpan pelanggan baru.
     *
     * @param Pelanggan $pelanggan
     * @return int ID pelanggan baru
     */
    public function create(Pelanggan $pelanggan): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO pelanggan (nama, no_telepon, email, alamat)
             VALUES (:nama, :no_telepon, :email, :alamat)"
        );
        $stmt->execute([
            'nama'       => $pelanggan->getNama(),
            'no_telepon' => $pelanggan->getKontak(),
            'email'      => $pelanggan->getEmail(),
            'alamat'     => $pelanggan->getAlamat(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Update data pelanggan.
     *
     * @param Pelanggan $pelanggan
     * @return bool
     */
    public function update(Pelanggan $pelanggan): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE pelanggan SET nama = :nama, no_telepon = :no_telepon,
                    email = :email, alamat = :alamat
             WHERE id = :id"
        );
        return $stmt->execute([
            'nama'       => $pelanggan->getNama(),
            'no_telepon' => $pelanggan->getKontak(),
            'email'      => $pelanggan->getEmail(),
            'alamat'     => $pelanggan->getAlamat(),
            'id'         => $pelanggan->getId(),
        ]);
    }

    /**
     * Hapus pelanggan.
     *
     * @param int $id
     * @return bool
     * @throws PDOException Jika pelanggan masih punya pesanan aktif
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM pelanggan WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * @param array $row
     * @return Pelanggan
     */
    private function mapToModel(array $row): Pelanggan
    {
        return new Pelanggan(
            nama: $row['nama'],
            kontak: $row['no_telepon'],
            id: (int) $row['id'],
            email: $row['email'],
            alamat: $row['alamat']
        );
    }
}
