<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Class PesananTakeAway — pesanan dibawa pulang.
 *
 * Mendemonstrasikan POLYMORPHISM: override method hitungTotal() dari parent.
 * Pada take-away, total = subtotal item saja (TANPA biaya layanan meja).
 *
 * @package App\Models
 */
class PesananTakeAway extends Pesanan
{
    /**
     * Constructor PesananTakeAway.
     *
     * @param int         $pelangganId ID pelanggan
     * @param string|null $catatan     Catatan tambahan
     */
    public function __construct(
        int $pelangganId = 0,
        ?string $catatan = null
    ) {
        // Take-away tidak butuh meja, jadi mejaId = null
        parent::__construct($pelangganId, null, $catatan);
    }

    /**
     * Hitung total pesanan take-away.
     *
     * POLYMORPHISM: berbeda dari PesananDineIn — tidak ada biaya layanan.
     * Total = subtotal semua item (tanpa tambahan biaya apapun).
     *
     * @return float Total yang harus dibayar
     */
    public function hitungTotal(): float
    {
        return $this->hitungSubtotalItems();
    }

    public function getTipePesanan(): string
    {
        return 'take_away';
    }
}
