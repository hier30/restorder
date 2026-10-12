# Dokumen Skenario Pengujian

## A. Unit Test Otomatis (PHPUnit)

Menjalankan semua unit test:
```bash
vendor/bin/phpunit
```

| No | Test Class | Method | Keterangan |
|----|-----------|--------|-----------|
| 1 | PesananTest | testHitungTotalDineInDenganBiayaLayanan | Total dine-in = subtotal + 10% layanan |
| 2 | PesananTest | testHitungTotalTakeAwayTanpaBiayaLayanan | Total take-away = subtotal saja |
| 3 | PesananTest | testPolymorphismDineInVsTakeAway | Item sama, total berbeda (bukti polymorphism) |
| 4 | PesananTest | testHitungTotalDenganJumlahBanyak | Multi item, jumlah > 1 |
| 5 | PesananTest | testHitungTotalTanpaItem | Edge: pesanan kosong → total 0 |
| 6 | PesananTest | testHitungTotalDenganHargaNol | Edge: harga gratis → total 0 |
| 7 | PesananTest | testDineInDenganPersenLayananKustom | Layanan 5% (bukan default 10%) |
| 8 | PesananTest | testGetTipePesanan | Return string 'dine_in' / 'take_away' |
| 9 | PesananTest | testGetterSetterPesanan | Encapsulation getter/setter |
| 10 | ValidasiServiceTest | testValidasiPelangganValid | Input lengkap → lolos |
| 11 | ValidasiServiceTest | testValidasiPelangganNamaKosong | Nama kosong → exception |
| 12 | ValidasiServiceTest | testValidasiPelangganEmailTidakValid | Email salah format → exception |
| 13 | ValidasiServiceTest | testValidasiPelangganFieldOpsionalKosong | Field opsional → null |
| 14 | ValidasiServiceTest | testValidasiMejaValid | Data meja valid |
| 15 | ValidasiServiceTest | testValidasiMejaNomorKosong | Nomor kosong → exception |
| 16 | ValidasiServiceTest | testValidasiMejaKapasitasDiluarRange | Kapasitas > 20 → exception |
| 17 | ValidasiServiceTest | testValidasiMenuValid | Data menu valid |
| 18 | ValidasiServiceTest | testValidasiMenuNamaKosong | Nama kosong → exception |
| 19 | ValidasiServiceTest | testValidasiPesananDineInValid | Pesanan dine-in lengkap |
| 20 | ValidasiServiceTest | testValidasiPesananDineInTanpaMeja | Dine-in tanpa meja → exception |
| 21 | ValidasiServiceTest | testValidasiPesananTanpaItem | Pesanan tanpa item → exception |
| 22 | ValidasiServiceTest | testValidasiPesananTakeAwayValid | Take-away tanpa meja → valid |
| 23 | PesananServiceTest | testProsesPesananPendingBerhasil | Pesanan pending → diproses |
| 24 | PesananServiceTest | testProsesPesananTidakDitemukan | Edge: ID tidak ada → exception, status tidak berubah |
| 25-27 | PesananServiceTest | testProsesPesananStatusSelainPendingDitolak | Edge: status diproses/selesai/dibatalkan → ditolak |

---

## B. Skenario Uji Manual (Fitur Browser/Database)

Fitur-fitur di bawah ini membutuhkan browser dan koneksi database untuk diuji.
Isi kolom **Hasil** dengan "OK" atau deskripsi error setelah Anda menjalankan pengujian.

### B.1 Setup & Koneksi Database

| No | Skenario | Langkah | Hasil yang Diharapkan | Hasil |
|----|----------|---------|----------------------|-------|
| 1 | Koneksi DB berhasil | Buka `http://localhost:8000` | Halaman dashboard muncul tanpa error | |
| 2 | Koneksi DB gagal | Ubah DB_PASSWORD di .env jadi salah, refresh | Pesan "Terjadi kesalahan pada server" | |

### B.2 Kelola Meja

| No | Skenario | Langkah | Hasil yang Diharapkan | Hasil |
|----|----------|---------|----------------------|-------|
| 1 | Lihat daftar meja | Klik menu "Kelola Meja" | Tampil kartu meja dengan status warna | |
| 2 | Tambah meja baru | Klik "Tambah Meja" → isi M06, kapasitas 4, status tersedia → Simpan | Meja M06 muncul di daftar | |
| 3 | Edit meja | Klik ikon edit pada M06 → ubah kapasitas jadi 6 → Simpan | Kapasitas berubah jadi 6 | |
| 4 | Reservasi meja | Klik "Reservasi" pada meja yang tersedia | Status berubah jadi "Dipesan" (warna kuning) | |
| 5 | Bebaskan meja | Klik "Bebaskan" pada meja yang terisi/dipesan | Status kembali ke "Tersedia" (warna hijau) | |
| 6 | Hapus meja | Klik ikon hapus → konfirmasi | Meja hilang dari daftar | |
| 7 | Tambah meja duplikat | Tambah meja dengan nomor yang sudah ada (misal M01) | Pesan error "Nomor meja mungkin sudah ada" | |

### B.3 Data Pelanggan (CRUD)

| No | Skenario | Langkah | Hasil yang Diharapkan | Hasil |
|----|----------|---------|----------------------|-------|
| 1 | Lihat daftar pelanggan | Klik menu "Pelanggan" | Tampil tabel pelanggan | |
| 2 | Tambah pelanggan | Klik "Tambah Pelanggan" → isi data → Simpan | Pelanggan baru muncul di daftar | |
| 3 | Tambah pelanggan tanpa nama | Kosongkan nama → Simpan | Alert error "Nama pelanggan wajib diisi" | |
| 4 | Edit pelanggan | Klik edit → ubah nama → Simpan | Nama berubah di daftar | |
| 5 | Hapus pelanggan | Klik hapus → konfirmasi | Pelanggan hilang dari daftar | |

### B.4 Daftar Menu

| No | Skenario | Langkah | Hasil yang Diharapkan | Hasil |
|----|----------|---------|----------------------|-------|
| 1 | Lihat daftar menu | Klik menu "Menu" | Tampil tabel menu dengan kategori dan harga | |
| 2 | Tambah menu | Klik "Tambah Menu" → isi data → Simpan | Menu baru muncul di daftar | |
| 3 | Edit menu | Klik edit → ubah harga → Simpan | Harga berubah | |
| 4 | Nonaktifkan menu | Edit menu → uncheck "Menu tersedia" → Simpan | Badge berubah jadi "Habis" | |

### B.5 Pesanan

| No | Skenario | Langkah | Hasil yang Diharapkan | Hasil |
|----|----------|---------|----------------------|-------|
| 1 | Buat pesanan dine-in | Buat Pesanan → pilih pelanggan, tipe Dine In, pilih meja, tambah 2 item → Buat Pesanan | Pesanan berhasil, total = subtotal + 10% layanan | |
| 2 | Buat pesanan take-away | Buat Pesanan → tipe Take Away → tambah item → Buat | Pesanan berhasil, meja tersembunyi, total = subtotal saja | |
| 3 | Hitung subtotal otomatis | Pilih menu dan ubah jumlah di form | Subtotal, biaya layanan, dan total berubah real-time | |
| 4 | Tambah/hapus item | Klik "Tambah Item", pilih menu; klik ikon hapus pada item | Item ditambah/dihapus dari form | |
| 5 | Lihat detail pesanan | Klik ikon mata di daftar pesanan | Tampil info pesanan + rincian item + total | |
| 5a | Proses pesanan | Klik ikon play biru pada pesanan berstatus Pending → OK | Status jadi "Diproses"; ikon play hilang, tombol Selesaikan/Batalkan tetap ada | |
| 6 | Selesaikan pesanan | Klik centang hijau di pesanan pending | Status jadi "Selesai", meja kembali tersedia | |
| 7 | Batalkan pesanan | Klik X kuning di pesanan pending | Status jadi "Dibatalkan" | |
| 8 | Pesanan tanpa item | Buat pesanan tanpa memilih menu → Submit | Pesan error "Minimal harus ada 1 item" | |
| 9 | Dine-in tanpa meja | Pilih Dine In, jangan pilih meja → Submit | Pesan error "Meja wajib dipilih" | |

### B.6 Laporan & Export PDF

| No | Skenario | Langkah | Hasil yang Diharapkan | Hasil |
|----|----------|---------|----------------------|-------|
| 1 | Lihat laporan semua pesanan | Klik menu "Laporan" | Tampil tabel semua pesanan + ringkasan | |
| 2 | Filter berdasarkan tanggal | Isi "Dari" dan "Sampai" → klik Filter | Hanya tampil pesanan dalam rentang tanggal | |
| 3 | Filter berdasarkan pelanggan | Pilih pelanggan dari dropdown → Filter | Hanya tampil pesanan pelanggan tersebut | |
| 4 | Export PDF | Klik tombol "Export PDF" | File PDF ter-download dengan data laporan | |
| 5 | Export PDF dengan filter | Set filter tanggal → Export PDF | PDF berisi data sesuai filter | |

### B.7 Dashboard

| No | Skenario | Langkah | Hasil yang Diharapkan | Hasil |
|----|----------|---------|----------------------|-------|
| 1 | Tampilkan ringkasan | Buka halaman dashboard | Kartu menampilkan: meja tersedia, total pelanggan, pesanan hari ini, pendapatan | |
| 2 | Pesanan terbaru | Lihat tabel di dashboard | Menampilkan 5 pesanan terbaru | |

---

## C. Catatan Pengujian

- **Tanggal pengujian**: ________________
- **Penguji**: ________________
- **Versi PHP**: ________________
- **Versi MySQL**: ________________
- **Browser**: ________________

### Kesimpulan:
- [ ] Semua unit test PHPUnit PASS
- [ ] Semua skenario manual sudah diuji
- [ ] Tidak ada error/bug yang belum terselesaikan
