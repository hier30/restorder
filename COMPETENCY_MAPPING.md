# Pemetaan Unit Kompetensi BNSP Programmer

Dokumen ini memetakan setiap unit kompetensi dalam skema sertifikasi BNSP Programmer ke file, class, dan method spesifik yang mengimplementasikannya dalam project Sistem Pemesanan Restoran.

---

## J.620100.016.01 — Membuat Kode Program Sesuai dengan Guidelines

**Tujuan**: Menulis kode yang rapi, konsisten, mengikuti standar penamaan dan konvensi.

| Bukti Implementasi | Lokasi |
|---------------------|--------|
| PSR-4 autoloading (standar namespace PHP) | `composer.json` → bagian `autoload` |
| `declare(strict_types=1)` di setiap file PHP | Semua file di `src/` |
| Penamaan class PascalCase, method camelCase | `src/Models/PesananDineIn.php`, `src/Services/ValidasiService.php` |
| Penamaan file sesuai nama class (PSR-4) | Semua file di `src/` |
| Struktur folder terorganisir (MVC pattern) | `src/Models/`, `src/Controllers/`, `src/Repositories/`, `src/Services/`, `views/` |
| PHPDoc pada setiap class dan method | Semua file di `src/` (lihat `@param`, `@return`, `@throws`) |
| Konfigurasi terpisah (bukan hardcode) | `config/database.php`, `.env.example` |

---

## J.620100.017.02 — Mengimplementasikan Pemrograman Terstruktur

**Tujuan**: Menggunakan percabangan, perulangan, dan logika terstruktur secara tepat.

| Bukti Implementasi | Lokasi |
|---------------------|--------|
| Percabangan (if/else) untuk validasi input | `src/Services/ValidasiService.php` → `validasiPelanggan()`, `validasiPesanan()` |
| Percabangan untuk tipe pesanan (dine-in vs take-away) | `src/Services/PesananService.php` → `buatPesanan()` baris percabangan OOP |
| Perulangan (foreach) untuk menghitung subtotal item | `src/Services/PesananService.php` → `buatPesanan()` loop `foreach ($validated['items'] ...)` |
| Perulangan untuk validasi setiap item pesanan | `src/Services/ValidasiService.php` → `validasiPesanan()` loop `foreach ($items ...)` |
| Logika kalkulasi terstruktur di layer Service | `src/Models/Pesanan.php` → `hitungSubtotalItems()` |
| Query builder dinamis dengan percabangan | `src/Repositories/PesananRepository.php` → `findForLaporan()` |

---

## J.620100.018.02 — Mengimplementasikan Pemrograman Berorientasi Objek (OOP)

**Tujuan**: Menerapkan encapsulation, inheritance, polymorphism, dan overloading.

| Konsep OOP | Bukti Implementasi | Lokasi |
|------------|---------------------|--------|
| **Encapsulation** | Semua property `private`/`protected` + getter/setter | `src/Models/Meja.php`, `src/Models/Menu.php`, `src/Models/Pelanggan.php` (semua model) |
| **Inheritance** | Abstract class `Pengguna` diturunkan ke `Pelanggan` dan `Admin` | `src/Models/Pengguna.php` → `src/Models/Pelanggan.php`, `src/Models/Admin.php` |
| **Polymorphism** | Abstract method `hitungTotal()` dioverride berbeda oleh `PesananDineIn` (+biaya layanan) dan `PesananTakeAway` (tanpa biaya layanan) | `src/Models/Pesanan.php` → `src/Models/PesananDineIn.php`, `src/Models/PesananTakeAway.php` |
| **Emulasi Overloading** | Default/optional parameter pada constructor Pengguna; `func_get_args()` + `func_num_args()` pada constructor Pesanan | `src/Models/Pengguna.php` constructor, `src/Models/Pesanan.php` constructor (lihat komentar "emulasi overloading ala PHP") |
| **Abstract Class** | `Pengguna` dan `Pesanan` tidak bisa di-instantiate langsung | `src/Models/Pengguna.php`, `src/Models/Pesanan.php` |

---

## J.620100.019.02 — Menggunakan Library/Komponen yang Sudah Ada (Reuse)

**Tujuan**: Memanfaatkan komponen/library yang sudah ada untuk efisiensi.

| Library/Komponen | Fungsi | Lokasi Penggunaan |
|------------------|--------|-------------------|
| **Dompdf v3.1** (Composer: `dompdf/dompdf`) | Export laporan pemesanan ke PDF | `src/Services/LaporanService.php` → `exportPdf()` |
| **vlucas/phpdotenv** (Composer) | Membaca konfigurasi `.env` | `config/database.php` |
| **Bootstrap 5.3** (CDN) | Framework CSS untuk tampilan UI | `views/layouts/main.php` |
| **Bootstrap Icons** (CDN) | Ikon navigasi dan tombol | `views/layouts/main.php` |
| **PHPUnit 10.5** (Composer dev) | Framework unit testing | `tests/`, `phpunit.xml` |
| **Composer Autoloader** | PSR-4 autoloading class | `composer.json`, `public/index.php` |

Catatan lisensi Dompdf: **LGPL** — boleh digunakan dalam project apapun.

---

## J.620100.021.02 — Mengimplementasikan Akses Basis Data

**Tujuan**: Mengakses database dengan aman dan efisien.

| Bukti Implementasi | Lokasi |
|---------------------|--------|
| Koneksi PDO dengan konfigurasi terpisah (`.env`) | `config/database.php` |
| PDO prepared statement (anti SQL injection) | Semua file di `src/Repositories/` — semua query pakai `$pdo->prepare()` + `execute()` |
| CRUD lengkap (Create, Read, Update, Delete) | `src/Repositories/MejaRepository.php`, `PelangganRepository.php`, `MenuRepository.php`, `PesananRepository.php` |
| Transaksi database (begin/commit/rollback) | `src/Repositories/PesananRepository.php` → `create()` |
| Foreign key constraint | `database/schema.sql` — `CONSTRAINT fk_pesanan_pelanggan`, `fk_pesanan_meja`, dll |
| Index untuk optimasi query laporan | `database/schema.sql` — `idx_pesanan_tanggal`, `idx_pesanan_pelanggan` |
| Repository/DAO pattern (pemisahan akses data) | Semua file di `src/Repositories/` |

---

## J.620100.023.02 — Membuat Dokumentasi Kode Program

**Tujuan**: Mendokumentasikan kode agar mudah dipahami dan di-maintain.

| Bukti Implementasi | Lokasi |
|---------------------|--------|
| PHPDoc pada setiap class | Semua file di `src/` (lihat `/** ... */` di atas setiap class) |
| PHPDoc pada setiap method (tujuan, `@param`, `@return`, `@throws`) | Semua file di `src/` (contoh: `PesananService.php` → `buatPesanan()`) |
| Komentar penjelasan konsep OOP | `src/Models/Pengguna.php`, `Pesanan.php`, `PesananDineIn.php` |
| README.md lengkap | `README.md` — deskripsi, ERD, cara setup, daftar modul |
| COMPETENCY_MAPPING.md | File ini |
| TESTING.md (skenario uji manual) | `TESTING.md` |

---

## J.620100.025.02 — Melakukan Debugging

**Tujuan**: Menangani error dengan baik dan menyediakan mekanisme debugging.

| Bukti Implementasi | Lokasi |
|---------------------|--------|
| Try-catch spesifik (`PDOException`, `InvalidArgumentException`) | `src/Controllers/MejaController.php` → `store()`, `src/Services/PesananService.php` → `buatPesanan()` |
| Error logging ke file | `config/app.php` → `error_log()`, `src/Controllers/BaseController.php` |
| Mode debug via `.env` (APP_DEBUG=true/false) | `config/app.php` — toggle `display_errors` |
| Pesan error ramah user (bukan stack trace) | Semua controller → `setFlash($e->getMessage(), 'danger')` |
| Error handling di koneksi database | `config/database.php` → catch `PDOException` |
| Transaksi rollback jika error | `src/Repositories/PesananRepository.php` → `create()` |

---

## J.620100.033.02 — Melakukan Unit Testing

**Tujuan**: Menguji fungsi/method penting secara otomatis.

| Bukti Implementasi | Lokasi |
|---------------------|--------|
| PHPUnit sebagai dev dependency | `composer.json` → `require-dev` |
| Test method `hitungTotal()` kasus normal | `tests/PesananTest.php` |
| Test method `hitungTotal()` kasus edge (tanpa item, item negatif) | `tests/PesananTest.php` |
| Test validasi input (kasus valid & invalid) | `tests/ValidasiServiceTest.php` |
| Konfigurasi PHPUnit | `phpunit.xml` |
| Skenario uji manual untuk fitur browser | `TESTING.md` |

---

## Ringkasan Pemetaan

| Unit Kompetensi | File/Modul Utama |
|----------------|-----------------|
| J.620100.016.01 (Guidelines) | `composer.json`, semua `src/`, `config/` |
| J.620100.017.02 (Terstruktur) | `ValidasiService.php`, `PesananService.php`, `PesananRepository.php` |
| J.620100.018.02 (OOP) | `Pengguna.php`, `Pelanggan.php`, `Admin.php`, `Pesanan.php`, `PesananDineIn.php`, `PesananTakeAway.php` |
| J.620100.019.02 (Reuse) | `LaporanService.php` (Dompdf), `composer.json`, `views/layouts/main.php` (Bootstrap) |
| J.620100.021.02 (Database) | `config/database.php`, semua `src/Repositories/`, `database/schema.sql` |
| J.620100.023.02 (Dokumentasi) | `README.md`, `COMPETENCY_MAPPING.md`, `TESTING.md`, PHPDoc di `src/` |
| J.620100.025.02 (Debugging) | `config/app.php`, semua `src/Controllers/`, `PesananRepository.php` |
| J.620100.033.02 (Unit Testing) | `tests/PesananTest.php`, `tests/ValidasiServiceTest.php`, `phpunit.xml` |
