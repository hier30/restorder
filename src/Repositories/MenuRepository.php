<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Menu;
use PDO;

/**
 * Repository untuk operasi CRUD tabel menu.
 *
 * @package App\Repositories
 */
class MenuRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Ambil semua menu.
     *
     * @return Menu[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            "SELECT * FROM menu ORDER BY kategori ASC, nama_menu ASC"
        );
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $result[] = $this->mapToModel($row);
        }
        return $result;
    }

    /**
     * Ambil menu yang tersedia saja.
     *
     * @return Menu[]
     */
    public function findTersedia(): array
    {
        $stmt = $this->pdo->query(
            "SELECT * FROM menu WHERE tersedia = 1 ORDER BY kategori ASC, nama_menu ASC"
        );
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $result[] = $this->mapToModel($row);
        }
        return $result;
    }

    /**
     * Cari menu berdasarkan ID.
     *
     * @param int $id
     * @return Menu|null
     */
    public function findById(int $id): ?Menu
    {
        $stmt = $this->pdo->prepare("SELECT * FROM menu WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->mapToModel($row) : null;
    }

    /**
     * Simpan menu baru.
     *
     * @param Menu $menu
     * @return int ID menu baru
     */
    public function create(Menu $menu): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO menu (nama_menu, kategori, harga, deskripsi, tersedia)
             VALUES (:nama_menu, :kategori, :harga, :deskripsi, :tersedia)"
        );
        $stmt->execute([
            'nama_menu' => $menu->getNamaMenu(),
            'kategori'  => $menu->getKategori(),
            'harga'     => $menu->getHarga(),
            'deskripsi' => $menu->getDeskripsi(),
            'tersedia'  => $menu->isTersedia() ? 1 : 0,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Update data menu.
     *
     * @param Menu $menu
     * @return bool
     */
    public function update(Menu $menu): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE menu SET nama_menu = :nama_menu, kategori = :kategori,
                    harga = :harga, deskripsi = :deskripsi, tersedia = :tersedia
             WHERE id = :id"
        );
        return $stmt->execute([
            'nama_menu' => $menu->getNamaMenu(),
            'kategori'  => $menu->getKategori(),
            'harga'     => $menu->getHarga(),
            'deskripsi' => $menu->getDeskripsi(),
            'tersedia'  => $menu->isTersedia() ? 1 : 0,
            'id'        => $menu->getId(),
        ]);
    }

    /**
     * Hapus menu.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM menu WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * @param array $row
     * @return Menu
     */
    private function mapToModel(array $row): Menu
    {
        return new Menu(
            namaMenu: $row['nama_menu'],
            kategori: $row['kategori'],
            harga: (float) $row['harga'],
            deskripsi: $row['deskripsi'],
            tersedia: (bool) $row['tersedia'],
            id: (int) $row['id']
        );
    }
}
