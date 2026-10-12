<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Class PesananDineIn — pesanan makan di tempat.
 *
 * Mendemonstrasikan POLYMORPHISM: override method hitungTotal() dari parent.
 * Pada dine-in, total = subtotal item + biaya layanan meja (service charge).
 *
 * @package App\Models
 */
class PesananDineIn extends Pesanan
{
    /** @var float Biaya layanan meja (service charge) untuk dine-in */
    private float $biayaLayanan;

    /** @var float Persentase biaya layanan (default 10%) */
    private float $persenLayanan;

    /**
     * Constructor PesananDineIn.
     *
     * @param int         $pelangganId ID pelanggan
     * @param int|null    $mejaId      ID meja (wajib untuk dine-in)
     * @param string|null $catatan     Catatan tambahan
     * @param float       $persenLayanan Persentase biaya layanan (default 0.10 = 10%)
     */
    public function __construct(
        int $pelangganId = 0,
        ?int $mejaId = null,
        ?string $catatan = null,
        float $persenLayanan = 0.10
    ) {
        parent::__construct($pelangganId, $mejaId, $catatan);
        $this->persenLayanan = $persenLayanan;
        $this->biayaLayanan  = 0.0;
    }

    /**
     * Hitung total pesanan dine-in.
     *
     * POLYMORPHISM: implementasi berbeda dari PesananTakeAway.
     * Total = subtotal semua item + biaya layanan (persentase dari subtotal).
     *
     * @return float Total yang harus dibayar
     */
    public function hitungTotal(): float
    {
        $subtotal = $this->hitungSubtotalItems();
        $this->biayaLayanan = $subtotal * $this->persenLayanan;
        return $subtotal + $this->biayaLayanan;
    }

    public function getTipePesanan(): string
    {
        return 'dine_in';
    }

    public function getBiayaLayanan(): float
    {
        return $this->biayaLayanan;
    }

    public function setBiayaLayanan(float $biayaLayanan): void
    {
        $this->biayaLayanan = $biayaLayanan;
    }

    public function getPersenLayanan(): float
    {
        return $this->persenLayanan;
    }

    public function setPersenLayanan(float $persenLayanan): void
    {
        $this->persenLayanan = $persenLayanan;
    }
}
