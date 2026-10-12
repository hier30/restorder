<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;

/**
 * Service untuk validasi input.
 *
 * Mendemonstrasikan PEMROGRAMAN TERSTRUKTUR:
 * Validasi menggunakan percabangan (if/else) dan loop (foreach) terpisah dari
 * controller dan repository — logika bisnis di layer service.
 *
 * @package App\Services
 */
class ValidasiService
{
    /**
     * Validasi data pelanggan.
     *
     * @param array $data ['nama', 'no_telepon', 'email', 'alamat']
     * @return array Data yang sudah dibersihkan (trimmed)
     * @throws InvalidArgumentException Jika validasi gagal
     */
    public function validasiPelanggan(array $data): array
    {
        $errors = [];

        $nama = trim($data['nama'] ?? '');
        if ($nama === '') {
            $errors[] = 'Nama pelanggan wajib diisi.';
        } elseif (strlen($nama) > 100) {
            $errors[] = 'Nama pelanggan maksimal 100 karakter.';
        }

        $noTelepon = trim($data['no_telepon'] ?? '');
        if ($noTelepon !== '' && !preg_match('/^[0-9+\-\s]{8,20}$/', $noTelepon)) {
            $errors[] = 'Nomor telepon tidak valid (hanya angka, +, -, spasi, 8-20 karakter).';
        }

        $email = trim($data['email'] ?? '');
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }

        if (count($errors) > 0) {
            throw new InvalidArgumentException(implode(' ', $errors));
        }

        return [
            'nama'       => $nama,
            'no_telepon' => $noTelepon ?: null,
            'email'      => $email ?: null,
            'alamat'     => trim($data['alamat'] ?? '') ?: null,
        ];
    }

    /**
     * Validasi data meja.
     *
     * @param array $data ['nomor_meja', 'kapasitas', 'status']
     * @return array Data yang sudah dibersihkan
     * @throws InvalidArgumentException Jika validasi gagal
     */
    public function validasiMeja(array $data): array
    {
        $errors = [];

        $nomorMeja = trim($data['nomor_meja'] ?? '');
        if ($nomorMeja === '') {
            $errors[] = 'Nomor meja wajib diisi.';
        }

        $kapasitas = (int) ($data['kapasitas'] ?? 0);
        if ($kapasitas < 1 || $kapasitas > 20) {
            $errors[] = 'Kapasitas meja harus antara 1-20 kursi.';
        }

        $statusValid = ['tersedia', 'terisi', 'dipesan'];
        $status = $data['status'] ?? 'tersedia';
        if (!in_array($status, $statusValid, true)) {
            $errors[] = 'Status meja tidak valid.';
        }

        if (count($errors) > 0) {
            throw new InvalidArgumentException(implode(' ', $errors));
        }

        return [
            'nomor_meja' => $nomorMeja,
            'kapasitas'  => $kapasitas,
            'status'     => $status,
        ];
    }

    /**
     * Validasi data menu.
     *
     * @param array $data ['nama_menu', 'kategori', 'harga', 'deskripsi', 'tersedia']
     * @return array Data yang sudah dibersihkan
     * @throws InvalidArgumentException Jika validasi gagal
     */
    public function validasiMenu(array $data): array
    {
        $errors = [];

        $namaMenu = trim($data['nama_menu'] ?? '');
        if ($namaMenu === '') {
            $errors[] = 'Nama menu wajib diisi.';
        }

        $kategoriValid = ['makanan', 'minuman', 'dessert'];
        $kategori = $data['kategori'] ?? 'makanan';
        if (!in_array($kategori, $kategoriValid, true)) {
            $errors[] = 'Kategori menu tidak valid.';
        }

        $harga = (float) ($data['harga'] ?? 0);
        if ($harga < 0) {
            $errors[] = 'Harga tidak boleh negatif.';
        }

        if (count($errors) > 0) {
            throw new InvalidArgumentException(implode(' ', $errors));
        }

        return [
            'nama_menu' => $namaMenu,
            'kategori'  => $kategori,
            'harga'     => $harga,
            'deskripsi' => trim($data['deskripsi'] ?? '') ?: null,
            'tersedia'  => isset($data['tersedia']) ? (bool) $data['tersedia'] : true,
        ];
    }

    /**
     * Validasi data pesanan.
     *
     * @param array $data ['pelanggan_id', 'tipe_pesanan', 'meja_id', 'items', 'catatan']
     * @return array Data yang sudah divalidasi
     * @throws InvalidArgumentException Jika validasi gagal
     */
    public function validasiPesanan(array $data): array
    {
        $errors = [];

        $pelangganId = (int) ($data['pelanggan_id'] ?? 0);
        if ($pelangganId <= 0) {
            $errors[] = 'Pelanggan wajib dipilih.';
        }

        $tipeValid = ['dine_in', 'take_away'];
        $tipePesanan = $data['tipe_pesanan'] ?? '';
        if (!in_array($tipePesanan, $tipeValid, true)) {
            $errors[] = 'Tipe pesanan harus dine_in atau take_away.';
        }

        if ($tipePesanan === 'dine_in') {
            $mejaId = (int) ($data['meja_id'] ?? 0);
            if ($mejaId <= 0) {
                $errors[] = 'Meja wajib dipilih untuk pesanan dine-in.';
            }
        }

        $items = $data['items'] ?? [];
        if (!is_array($items) || count($items) === 0) {
            $errors[] = 'Minimal harus ada 1 item menu dalam pesanan.';
        } else {
            foreach ($items as $i => $item) {
                $no = $i + 1;
                if (empty($item['menu_id'])) {
                    $errors[] = "Item #{$no}: menu belum dipilih.";
                }
                $jumlah = (int) ($item['jumlah'] ?? 0);
                if ($jumlah < 1) {
                    $errors[] = "Item #{$no}: jumlah harus minimal 1.";
                }
            }
        }

        if (count($errors) > 0) {
            throw new InvalidArgumentException(implode(' ', $errors));
        }

        return [
            'pelanggan_id' => $pelangganId,
            'tipe_pesanan' => $tipePesanan,
            'meja_id'      => ($tipePesanan === 'dine_in') ? (int) $data['meja_id'] : null,
            'items'        => $items,
            'catatan'      => trim($data['catatan'] ?? '') ?: null,
        ];
    }
}
