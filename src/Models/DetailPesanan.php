<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Class DetailPesanan — merepresentasikan satu baris item di dalam pesanan.
 *
 * Encapsulation: semua property private, diakses lewat getter/setter.
 *
 * @package App\Models
 */
class DetailPesanan
{
    /** @var int|null ID detail pesanan */
    private ?int $id;

    /** @var int ID pesanan induk */
    private int $pesananId;

    /** @var int ID menu yang dipesan */
    private int $menuId;

    /** @var int Jumlah item */
    private int $jumlah;

    /** @var float Harga menu saat dipesan (snapshot, tidak berubah walau harga menu diupdate) */
    private float $hargaSaatPesan;

    /** @var float Subtotal = jumlah * hargaSaatPesan */
    private float $subtotal;

    /** @var string|null Nama menu (untuk keperluan tampilan, tidak disimpan di DB) */
    private ?string $namaMenu;

    public function __construct(
        int $pesananId = 0,
        int $menuId = 0,
        int $jumlah = 1,
        float $hargaSaatPesan = 0.0,
        ?int $id = null
    ) {
        $this->pesananId     = $pesananId;
        $this->menuId        = $menuId;
        $this->jumlah        = $jumlah;
        $this->hargaSaatPesan = $hargaSaatPesan;
        $this->subtotal      = $jumlah * $hargaSaatPesan;
        $this->id            = $id;
        $this->namaMenu      = null;
    }

    /**
     * Hitung ulang subtotal berdasarkan jumlah dan harga saat pesan.
     *
     * @return float Subtotal terbaru
     */
    public function hitungSubtotal(): float
    {
        $this->subtotal = $this->jumlah * $this->hargaSaatPesan;
        return $this->subtotal;
    }

    // === GETTER & SETTER ===

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getPesananId(): int
    {
        return $this->pesananId;
    }

    public function setPesananId(int $pesananId): void
    {
        $this->pesananId = $pesananId;
    }

    public function getMenuId(): int
    {
        return $this->menuId;
    }

    public function setMenuId(int $menuId): void
    {
        $this->menuId = $menuId;
    }

    public function getJumlah(): int
    {
        return $this->jumlah;
    }

    public function setJumlah(int $jumlah): void
    {
        $this->jumlah = $jumlah;
        $this->hitungSubtotal();
    }

    public function getHargaSaatPesan(): float
    {
        return $this->hargaSaatPesan;
    }

    public function setHargaSaatPesan(float $hargaSaatPesan): void
    {
        $this->hargaSaatPesan = $hargaSaatPesan;
        $this->hitungSubtotal();
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function getNamaMenu(): ?string
    {
        return $this->namaMenu;
    }

    public function setNamaMenu(?string $namaMenu): void
    {
        $this->namaMenu = $namaMenu;
    }
}
