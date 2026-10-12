<?php

declare(strict_types=1);

namespace Tests;

use App\Models\PesananDineIn;
use App\Models\PesananTakeAway;
use PHPUnit\Framework\TestCase;

/**
 * Unit test untuk class Pesanan (PesananDineIn & PesananTakeAway).
 *
 * Menguji method hitungTotal() yang mendemonstrasikan polymorphism —
 * hasil berbeda tergantung tipe pesanan (dine-in ada biaya layanan, take-away tidak).
 */
class PesananTest extends TestCase
{
    // ====================================================================
    // KASUS NORMAL (happy path)
    // ====================================================================

    /**
     * Test: PesananDineIn harus menghitung total = subtotal + biaya layanan 10%.
     */
    public function testHitungTotalDineInDenganBiayaLayanan(): void
    {
        $pesanan = new PesananDineIn(pelangganId: 1, mejaId: 3);

        $pesanan->tambahItem(['menu_id' => 1, 'jumlah' => 1, 'harga_saat_pesan' => 35000]);
        $pesanan->tambahItem(['menu_id' => 2, 'jumlah' => 1, 'harga_saat_pesan' => 45000]);
        $pesanan->tambahItem(['menu_id' => 4, 'jumlah' => 1, 'harga_saat_pesan' => 8000]);

        // Subtotal = 35000 + 45000 + 8000 = 88000
        // Biaya layanan 10% = 8800
        // Total = 96800
        $total = $pesanan->hitungTotal();

        $this->assertEquals(96800.0, $total);
        $this->assertEquals(8800.0, $pesanan->getBiayaLayanan());
    }

    /**
     * Test: PesananTakeAway harus menghitung total = subtotal saja (tanpa biaya layanan).
     */
    public function testHitungTotalTakeAwayTanpaBiayaLayanan(): void
    {
        $pesanan = new PesananTakeAway(pelangganId: 2);

        $pesanan->tambahItem(['menu_id' => 1, 'jumlah' => 1, 'harga_saat_pesan' => 35000]);
        $pesanan->tambahItem(['menu_id' => 2, 'jumlah' => 1, 'harga_saat_pesan' => 45000]);
        $pesanan->tambahItem(['menu_id' => 4, 'jumlah' => 1, 'harga_saat_pesan' => 8000]);

        // Subtotal = 35000 + 45000 + 8000 = 88000
        // Tanpa biaya layanan → Total = 88000
        $total = $pesanan->hitungTotal();

        $this->assertEquals(88000.0, $total);
    }

    /**
     * Test POLYMORPHISM: item sama, tipe berbeda → total berbeda.
     *
     * Ini adalah bukti langsung bahwa method hitungTotal() berperilaku berbeda
     * pada PesananDineIn vs PesananTakeAway (polymorphism).
     */
    public function testPolymorphismDineInVsTakeAway(): void
    {
        $items = [
            ['menu_id' => 1, 'jumlah' => 2, 'harga_saat_pesan' => 40000],
        ];

        $dineIn = new PesananDineIn(1, 3);
        $takeAway = new PesananTakeAway(1);

        foreach ($items as $item) {
            $dineIn->tambahItem($item);
            $takeAway->tambahItem($item);
        }

        $totalDineIn = $dineIn->hitungTotal();     // 80000 + 10% = 88000
        $totalTakeAway = $takeAway->hitungTotal(); // 80000

        $this->assertGreaterThan($totalTakeAway, $totalDineIn);
        $this->assertEquals(88000.0, $totalDineIn);
        $this->assertEquals(80000.0, $totalTakeAway);
    }

    /**
     * Test: Beberapa item dengan jumlah > 1.
     */
    public function testHitungTotalDenganJumlahBanyak(): void
    {
        $pesanan = new PesananTakeAway(1);

        $pesanan->tambahItem(['menu_id' => 1, 'jumlah' => 3, 'harga_saat_pesan' => 10000]);
        $pesanan->tambahItem(['menu_id' => 2, 'jumlah' => 2, 'harga_saat_pesan' => 25000]);

        // 3*10000 + 2*25000 = 30000 + 50000 = 80000
        $this->assertEquals(80000.0, $pesanan->hitungTotal());
    }

    // ====================================================================
    // KASUS EDGE / ERROR
    // ====================================================================

    /**
     * Test edge case: Pesanan tanpa item apapun → total harus 0.
     */
    public function testHitungTotalTanpaItem(): void
    {
        $dineIn = new PesananDineIn(1, 3);
        $takeAway = new PesananTakeAway(1);

        $this->assertEquals(0.0, $dineIn->hitungTotal());
        $this->assertEquals(0.0, $takeAway->hitungTotal());
        $this->assertEquals(0.0, $dineIn->getBiayaLayanan());
    }

    /**
     * Test: Item dengan harga 0 (gratis/promo).
     */
    public function testHitungTotalDenganHargaNol(): void
    {
        $pesanan = new PesananTakeAway(1);
        $pesanan->tambahItem(['menu_id' => 1, 'jumlah' => 1, 'harga_saat_pesan' => 0]);

        $this->assertEquals(0.0, $pesanan->hitungTotal());
    }

    /**
     * Test: Persentase biaya layanan bisa dikustomisasi (bukan selalu 10%).
     */
    public function testDineInDenganPersenLayananKustom(): void
    {
        $pesanan = new PesananDineIn(1, 3, null, 0.05); // 5% layanan

        $pesanan->tambahItem(['menu_id' => 1, 'jumlah' => 1, 'harga_saat_pesan' => 100000]);

        // Subtotal = 100000, Layanan 5% = 5000, Total = 105000
        $this->assertEquals(105000.0, $pesanan->hitungTotal());
        $this->assertEquals(5000.0, $pesanan->getBiayaLayanan());
    }

    /**
     * Test: getTipePesanan() mengembalikan string yang benar.
     */
    public function testGetTipePesanan(): void
    {
        $dineIn = new PesananDineIn(1, 3);
        $takeAway = new PesananTakeAway(1);

        $this->assertEquals('dine_in', $dineIn->getTipePesanan());
        $this->assertEquals('take_away', $takeAway->getTipePesanan());
    }

    /**
     * Test: Getter dan setter berfungsi (encapsulation).
     */
    public function testGetterSetterPesanan(): void
    {
        $pesanan = new PesananDineIn(1, 3, 'catatan test');

        $this->assertEquals(1, $pesanan->getPelangganId());
        $this->assertEquals(3, $pesanan->getMejaId());
        $this->assertEquals('catatan test', $pesanan->getCatatan());
        $this->assertEquals('pending', $pesanan->getStatusPesanan());

        $pesanan->setStatusPesanan('selesai');
        $this->assertEquals('selesai', $pesanan->getStatusPesanan());

        $pesanan->setId(99);
        $this->assertEquals(99, $pesanan->getId());
    }
}
