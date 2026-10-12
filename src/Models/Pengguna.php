<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Abstract class Pengguna — kelas dasar untuk semua pengguna sistem.
 *
 * Mendemonstrasikan INHERITANCE: kelas ini diturunkan ke Pelanggan dan Admin.
 * Semua property bersifat protected (encapsulation) dan diakses melalui getter/setter.
 *
 * @package App\Models
 */
abstract class Pengguna
{
    /** @var int|null ID pengguna di database */
    protected ?int $id;

    /** @var string Nama lengkap pengguna */
    protected string $nama;

    /** @var string|null Nomor telepon */
    protected ?string $kontak;

    /**
     * Constructor Pengguna.
     *
     * Mendemonstrasikan EMULASI OVERLOADING ala PHP:
     * PHP tidak mendukung method overloading murni seperti Java/C# (tidak bisa
     * dua constructor dengan parameter berbeda). Sebagai gantinya, kita gunakan
     * default/optional parameter. Dengan cara ini, constructor bisa dipanggil
     * dengan 0, 1, 2, atau 3 argumen — mirip efek overloading di Java.
     *
     * Contoh pemanggilan:
     *   new Pelanggan()                          → semua default
     *   new Pelanggan('Budi')                    → hanya nama
     *   new Pelanggan('Budi', '081234567890')     → nama + kontak
     *   new Pelanggan('Budi', '081234567890', 1)  → nama + kontak + id
     *
     * @param string      $nama   Nama pengguna (default: string kosong)
     * @param string|null $kontak Nomor telepon (default: null)
     * @param int|null    $id     ID dari database (default: null, untuk record baru)
     */
    public function __construct(
        string $nama = '',
        ?string $kontak = null,
        ?int $id = null
    ) {
        $this->nama   = $nama;
        $this->kontak = $kontak;
        $this->id     = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getNama(): string
    {
        return $this->nama;
    }

    public function setNama(string $nama): void
    {
        $this->nama = $nama;
    }

    public function getKontak(): ?string
    {
        return $this->kontak;
    }

    public function setKontak(?string $kontak): void
    {
        $this->kontak = $kontak;
    }

    /**
     * Mengembalikan informasi pengguna sebagai string.
     * Method abstract — setiap turunan WAJIB mengimplementasikan sendiri.
     *
     * @return string Deskripsi singkat pengguna
     */
    abstract public function getInfo(): string;
}
