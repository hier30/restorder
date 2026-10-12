<?php

declare(strict_types=1);

namespace Tests;

use App\Services\ValidasiService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Unit test untuk ValidasiService.
 *
 * Menguji validasi input menggunakan percabangan dan perulangan
 * (pemrograman terstruktur) — kasus valid dan invalid.
 */
class ValidasiServiceTest extends TestCase
{
    private ValidasiService $validasi;

    protected function setUp(): void
    {
        $this->validasi = new ValidasiService();
    }

    // ====================================================================
    // VALIDASI PELANGGAN
    // ====================================================================

    /**
     * Test: Data pelanggan valid → tidak throw exception.
     */
    public function testValidasiPelangganValid(): void
    {
        $data = [
            'nama'       => 'Budi Santoso',
            'no_telepon' => '081234567890',
            'email'      => 'budi@email.com',
            'alamat'     => 'Jl. Merdeka No. 10',
        ];

        $result = $this->validasi->validasiPelanggan($data);

        $this->assertEquals('Budi Santoso', $result['nama']);
        $this->assertEquals('081234567890', $result['no_telepon']);
        $this->assertEquals('budi@email.com', $result['email']);
    }

    /**
     * Test: Nama kosong → harus throw InvalidArgumentException.
     */
    public function testValidasiPelangganNamaKosong(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Nama pelanggan wajib diisi');

        $this->validasi->validasiPelanggan(['nama' => '', 'no_telepon' => '']);
    }

    /**
     * Test: Email tidak valid → harus throw exception.
     */
    public function testValidasiPelangganEmailTidakValid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('email tidak valid');

        $this->validasi->validasiPelanggan([
            'nama' => 'Budi',
            'email' => 'bukan-email',
        ]);
    }

    /**
     * Test: Field opsional kosong → diisi null, tidak error.
     */
    public function testValidasiPelangganFieldOpsionalKosong(): void
    {
        $result = $this->validasi->validasiPelanggan([
            'nama' => 'Budi',
        ]);

        $this->assertEquals('Budi', $result['nama']);
        $this->assertNull($result['no_telepon']);
        $this->assertNull($result['email']);
        $this->assertNull($result['alamat']);
    }

    // ====================================================================
    // VALIDASI MEJA
    // ====================================================================

    /**
     * Test: Data meja valid.
     */
    public function testValidasiMejaValid(): void
    {
        $result = $this->validasi->validasiMeja([
            'nomor_meja' => 'M01',
            'kapasitas'  => 4,
            'status'     => 'tersedia',
        ]);

        $this->assertEquals('M01', $result['nomor_meja']);
        $this->assertEquals(4, $result['kapasitas']);
    }

    /**
     * Test: Nomor meja kosong → error.
     */
    public function testValidasiMejaNomorKosong(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->validasi->validasiMeja([
            'nomor_meja' => '',
            'kapasitas'  => 4,
        ]);
    }

    /**
     * Test: Kapasitas di luar range → error.
     */
    public function testValidasiMejaKapasitasDiluarRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Kapasitas meja harus antara 1-20');

        $this->validasi->validasiMeja([
            'nomor_meja' => 'M01',
            'kapasitas'  => 25,
        ]);
    }

    // ====================================================================
    // VALIDASI MENU
    // ====================================================================

    /**
     * Test: Data menu valid.
     */
    public function testValidasiMenuValid(): void
    {
        $result = $this->validasi->validasiMenu([
            'nama_menu' => 'Nasi Goreng',
            'kategori'  => 'makanan',
            'harga'     => 35000,
        ]);

        $this->assertEquals('Nasi Goreng', $result['nama_menu']);
        $this->assertEquals(35000.0, $result['harga']);
    }

    /**
     * Test: Nama menu kosong → error.
     */
    public function testValidasiMenuNamaKosong(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->validasi->validasiMenu([
            'nama_menu' => '',
            'kategori'  => 'makanan',
            'harga'     => 35000,
        ]);
    }

    // ====================================================================
    // VALIDASI PESANAN
    // ====================================================================

    /**
     * Test: Data pesanan dine-in valid.
     */
    public function testValidasiPesananDineInValid(): void
    {
        $result = $this->validasi->validasiPesanan([
            'pelanggan_id' => 1,
            'tipe_pesanan' => 'dine_in',
            'meja_id'      => 3,
            'items'        => [
                ['menu_id' => 1, 'jumlah' => 2],
            ],
        ]);

        $this->assertEquals(1, $result['pelanggan_id']);
        $this->assertEquals('dine_in', $result['tipe_pesanan']);
        $this->assertEquals(3, $result['meja_id']);
    }

    /**
     * Test: Pesanan dine-in tanpa meja → error.
     */
    public function testValidasiPesananDineInTanpaMeja(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Meja wajib dipilih');

        $this->validasi->validasiPesanan([
            'pelanggan_id' => 1,
            'tipe_pesanan' => 'dine_in',
            'meja_id'      => 0,
            'items'        => [['menu_id' => 1, 'jumlah' => 1]],
        ]);
    }

    /**
     * Test: Pesanan tanpa item → error.
     */
    public function testValidasiPesananTanpaItem(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Minimal harus ada 1 item');

        $this->validasi->validasiPesanan([
            'pelanggan_id' => 1,
            'tipe_pesanan' => 'take_away',
            'items'        => [],
        ]);
    }

    /**
     * Test: Pesanan take-away valid (tanpa meja).
     */
    public function testValidasiPesananTakeAwayValid(): void
    {
        $result = $this->validasi->validasiPesanan([
            'pelanggan_id' => 2,
            'tipe_pesanan' => 'take_away',
            'items'        => [
                ['menu_id' => 1, 'jumlah' => 1],
            ],
        ]);

        $this->assertEquals('take_away', $result['tipe_pesanan']);
        $this->assertNull($result['meja_id']);
    }
}
