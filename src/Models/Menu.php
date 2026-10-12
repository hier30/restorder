<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Class Menu — merepresentasikan item menu restoran.
 *
 * Encapsulation: semua property private, diakses lewat getter/setter.
 *
 * @package App\Models
 */
class Menu
{
    /** @var int|null ID menu */
    private ?int $id;

    /** @var string Nama menu */
    private string $namaMenu;

    /** @var string Kategori: makanan, minuman, dessert */
    private string $kategori;

    /** @var float Harga satuan dalam Rupiah */
    private float $harga;

    /** @var string|null Deskripsi menu */
    private ?string $deskripsi;

    /** @var bool Apakah menu tersedia */
    private bool $tersedia;

    public function __construct(
        string $namaMenu = '',
        string $kategori = 'makanan',
        float $harga = 0.0,
        ?string $deskripsi = null,
        bool $tersedia = true,
        ?int $id = null
    ) {
        $this->namaMenu  = $namaMenu;
        $this->kategori  = $kategori;
        $this->harga     = $harga;
        $this->deskripsi = $deskripsi;
        $this->tersedia  = $tersedia;
        $this->id        = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getNamaMenu(): string
    {
        return $this->namaMenu;
    }

    public function setNamaMenu(string $namaMenu): void
    {
        $this->namaMenu = $namaMenu;
    }

    public function getKategori(): string
    {
        return $this->kategori;
    }

    public function setKategori(string $kategori): void
    {
        $this->kategori = $kategori;
    }

    public function getHarga(): float
    {
        return $this->harga;
    }

    public function setHarga(float $harga): void
    {
        $this->harga = $harga;
    }

    public function getDeskripsi(): ?string
    {
        return $this->deskripsi;
    }

    public function setDeskripsi(?string $deskripsi): void
    {
        $this->deskripsi = $deskripsi;
    }

    public function isTersedia(): bool
    {
        return $this->tersedia;
    }

    public function setTersedia(bool $tersedia): void
    {
        $this->tersedia = $tersedia;
    }
}
