<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Abstract class Pesanan — kelas dasar untuk pesanan restoran.
 *
 * Mendemonstrasikan POLYMORPHISM: method hitungTotal() bersifat abstract
 * dan diimplementasikan berbeda oleh PesananDineIn (ada biaya layanan)
 * dan PesananTakeAway (tanpa biaya layanan).
 *
 * @package App\Models
 */
abstract class Pesanan
{
    /** @var int|null ID pesanan di database */
    protected ?int $id;

    /** @var int ID pelanggan yang memesan */
    protected int $pelangganId;

    /** @var int|null ID meja (null jika take-away) */
    protected ?int $mejaId;

    /** @var string Status pesanan: pending, diproses, selesai, dibatalkan */
    protected string $statusPesanan;

    /** @var string|null Tanggal & waktu pesanan */
    protected ?string $tanggalPesanan;

    /** @var string|null Catatan tambahan dari pelanggan */
    protected ?string $catatan;

    /** @var array Daftar item pesanan (detail_pesanan) */
    protected array $items;

    /**
     * Constructor Pesanan.
     *
     * Mendemonstrasikan EMULASI OVERLOADING ala PHP:
     * Menggunakan func_num_args() dan func_get_args() untuk membuat constructor
     * yang berperilaku berbeda tergantung jumlah argumen yang diberikan.
     *
     * PHP tidak mendukung method overloading murni seperti Java (dua method
     * dengan nama sama tapi parameter berbeda). Ini adalah cara PHP mengemulasikan
     * fitur tersebut — satu method yang mengecek jumlah argumen.
     *
     * Pemanggilan:
     *   new PesananDineIn(1)                → hanya pelangganId
     *   new PesananDineIn(1, 3)             → pelangganId + mejaId
     *   new PesananDineIn(1, 3, 'catatan')  → pelangganId + mejaId + catatan
     */
    public function __construct()
    {
        // Emulasi overloading: perilaku berbeda berdasarkan jumlah argumen
        $args    = func_get_args();
        $numArgs = func_num_args();

        $this->id             = null;
        $this->statusPesanan  = 'pending';
        $this->tanggalPesanan = date('Y-m-d H:i:s');
        $this->catatan        = null;
        $this->items          = [];
        $this->pelangganId    = 0;
        $this->mejaId         = null;

        if ($numArgs >= 1) {
            $this->pelangganId = (int) $args[0];
        }
        if ($numArgs >= 2) {
            $this->mejaId = $args[1] !== null ? (int) $args[1] : null;
        }
        if ($numArgs >= 3) {
            $this->catatan = (string) $args[2];
        }
    }

    /**
     * Menghitung total pesanan.
     *
     * ABSTRACT METHOD — inilah inti POLYMORPHISM:
     * - PesananDineIn: total = sum(subtotal item) + biaya layanan meja
     * - PesananTakeAway: total = sum(subtotal item) saja, tanpa biaya layanan
     *
     * @return float Total harga pesanan
     */
    abstract public function hitungTotal(): float;

    /**
     * Mengembalikan tipe pesanan sebagai string.
     *
     * @return string 'dine_in' atau 'take_away'
     */
    abstract public function getTipePesanan(): string;

    /**
     * Menambahkan item ke daftar pesanan.
     *
     * @param array $item ['menu_id' => int, 'jumlah' => int, 'harga_saat_pesan' => float]
     */
    public function tambahItem(array $item): void
    {
        $item['subtotal'] = ($item['jumlah'] ?? 1) * ($item['harga_saat_pesan'] ?? 0);
        $this->items[] = $item;
    }

    /**
     * Menghitung subtotal dari semua item (tanpa biaya layanan).
     *
     * @return float Total harga semua item
     */
    protected function hitungSubtotalItems(): float
    {
        $subtotal = 0.0;
        foreach ($this->items as $item) {
            $subtotal += (float) ($item['subtotal'] ?? 0);
        }
        return $subtotal;
    }

    // === GETTER & SETTER (Encapsulation) ===

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getPelangganId(): int
    {
        return $this->pelangganId;
    }

    public function setPelangganId(int $pelangganId): void
    {
        $this->pelangganId = $pelangganId;
    }

    public function getMejaId(): ?int
    {
        return $this->mejaId;
    }

    public function setMejaId(?int $mejaId): void
    {
        $this->mejaId = $mejaId;
    }

    public function getStatusPesanan(): string
    {
        return $this->statusPesanan;
    }

    public function setStatusPesanan(string $statusPesanan): void
    {
        $this->statusPesanan = $statusPesanan;
    }

    public function getTanggalPesanan(): ?string
    {
        return $this->tanggalPesanan;
    }

    public function setTanggalPesanan(?string $tanggalPesanan): void
    {
        $this->tanggalPesanan = $tanggalPesanan;
    }

    public function getCatatan(): ?string
    {
        return $this->catatan;
    }

    public function setCatatan(?string $catatan): void
    {
        $this->catatan = $catatan;
    }

    /**
     * @return array Semua item dalam pesanan
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array $items Daftar item pesanan
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }
}
