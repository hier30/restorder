<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\PesananRepository;
use App\Repositories\PelangganRepository;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Service untuk generate laporan dan export ke PDF.
 *
 * Mendemonstrasikan REUSE LIBRARY: menggunakan Dompdf (library pre-existing
 * dari Composer) untuk mengkonversi HTML menjadi PDF, alih-alih menulis
 * PDF generator sendiri dari nol.
 *
 * @package App\Services
 */
class LaporanService
{
    private PesananRepository $pesananRepo;
    private PelangganRepository $pelangganRepo;

    public function __construct(
        PesananRepository $pesananRepo,
        PelangganRepository $pelangganRepo
    ) {
        $this->pesananRepo   = $pesananRepo;
        $this->pelangganRepo = $pelangganRepo;
    }

    /**
     * Ambil data laporan pesanan berdasarkan filter.
     *
     * @param string|null $tanggalMulai
     * @param string|null $tanggalAkhir
     * @param int|null    $pelangganId
     * @return array ['pesanan' => array, 'ringkasan' => array, 'pelangganList' => array]
     */
    public function getDataLaporan(
        ?string $tanggalMulai = null,
        ?string $tanggalAkhir = null,
        ?int $pelangganId = null
    ): array {
        return [
            'pesanan'       => $this->pesananRepo->findForLaporan(
                $tanggalMulai, $tanggalAkhir, $pelangganId
            ),
            'ringkasan'     => $this->pesananRepo->hitungRingkasanLaporan(
                $tanggalMulai, $tanggalAkhir, $pelangganId
            ),
            'pelangganList' => $this->pelangganRepo->findAll(),
        ];
    }

    /**
     * Export laporan pesanan ke PDF menggunakan Dompdf.
     *
     * Dompdf digunakan karena:
     * 1. Sudah teruji dan stabil (library open-source populer)
     * 2. Cukup menerima string HTML lalu mengkonversinya ke PDF
     * 3. Jauh lebih efisien dibanding menulis PDF generator sendiri
     * 4. Lisensi LGPL — boleh digunakan dalam project apapun
     *
     * @param string|null $tanggalMulai
     * @param string|null $tanggalAkhir
     * @param int|null    $pelangganId
     */
    public function exportPdf(
        ?string $tanggalMulai = null,
        ?string $tanggalAkhir = null,
        ?int $pelangganId = null
    ): void {
        $data = $this->getDataLaporan($tanggalMulai, $tanggalAkhir, $pelangganId);

        $html = $this->buildLaporanHtml($data, $tanggalMulai, $tanggalAkhir);

        // Konfigurasi Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'sans-serif');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Kirim PDF ke browser sebagai download
        $dompdf->stream('laporan_pesanan_' . date('Y-m-d_His') . '.pdf', [
            'Attachment' => true,
        ]);
    }

    /**
     * Bangun HTML untuk laporan PDF.
     *
     * @param array       $data
     * @param string|null $tanggalMulai
     * @param string|null $tanggalAkhir
     * @return string HTML string
     */
    private function buildLaporanHtml(
        array $data,
        ?string $tanggalMulai,
        ?string $tanggalAkhir
    ): string {
        $pesanan   = $data['pesanan'];
        $ringkasan = $data['ringkasan'];

        $periodeText = 'Semua periode';
        if ($tanggalMulai && $tanggalAkhir) {
            $periodeText = "{$tanggalMulai} s/d {$tanggalAkhir}";
        } elseif ($tanggalMulai) {
            $periodeText = "Dari {$tanggalMulai}";
        } elseif ($tanggalAkhir) {
            $periodeText = "Sampai {$tanggalAkhir}";
        }
        // Tanggal berasal dari query string (input user), jadi harus di-escape
        $periodeText = htmlspecialchars($periodeText);

        $rows = '';
        $no = 1;
        foreach ($pesanan as $p) {
            $tipe = $p['tipe_pesanan'] === 'dine_in' ? 'Dine In' : 'Take Away';
            $meja = htmlspecialchars($p['nomor_meja'] ?? '-');
            $status = htmlspecialchars(ucfirst($p['status_pesanan']));
            $namaPelanggan = htmlspecialchars($p['nama_pelanggan'] ?? '-');
            $total = number_format((float) $p['total'], 0, ',', '.');
            $tanggal = date('d/m/Y H:i', strtotime($p['tanggal_pesanan']));

            $rows .= "<tr>
                <td style='text-align:center'>{$no}</td>
                <td>{$tanggal}</td>
                <td>{$namaPelanggan}</td>
                <td style='text-align:center'>{$tipe}</td>
                <td style='text-align:center'>{$meja}</td>
                <td style='text-align:center'>{$status}</td>
                <td style='text-align:right'>Rp {$total}</td>
            </tr>";
            $no++;
        }

        if (empty($pesanan)) {
            $rows = "<tr><td colspan='7' style='text-align:center'>Tidak ada data pesanan.</td></tr>";
        }

        $totalPendapatan = number_format((float) $ringkasan['total_pendapatan'], 0, ',', '.');
        $jumlahPesanan   = (int) $ringkasan['jumlah_pesanan'];

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Laporan Pesanan</title>
            <style>
                body { font-family: sans-serif; font-size: 12px; margin: 20px; }
                h1 { text-align: center; font-size: 18px; margin-bottom: 5px; }
                .subtitle { text-align: center; font-size: 12px; color: #666; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { border: 1px solid #333; padding: 6px 8px; }
                th { background-color: #2c3e50; color: #fff; text-align: center; }
                tr:nth-child(even) { background-color: #f2f2f2; }
                .ringkasan { margin-top: 10px; font-size: 13px; }
                .ringkasan td { border: none; padding: 3px 8px; }
                .footer { text-align: center; font-size: 10px; color: #999; margin-top: 30px; }
            </style>
        </head>
        <body>
            <h1>Laporan Pesanan Restoran</h1>
            <p class="subtitle">Periode: {$periodeText} | Dicetak: {$this->formatTanggalIndo(date('Y-m-d H:i:s'))}</p>

            <table>
                <thead>
                    <tr>
                        <th style="width:5%">No</th>
                        <th style="width:15%">Tanggal</th>
                        <th style="width:20%">Pelanggan</th>
                        <th style="width:12%">Tipe</th>
                        <th style="width:10%">Meja</th>
                        <th style="width:13%">Status</th>
                        <th style="width:15%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    {$rows}
                </tbody>
            </table>

            <table class="ringkasan">
                <tr>
                    <td><strong>Jumlah Pesanan:</strong></td>
                    <td>{$jumlahPesanan} pesanan</td>
                </tr>
                <tr>
                    <td><strong>Total Pendapatan:</strong></td>
                    <td><strong>Rp {$totalPendapatan}</strong></td>
                </tr>
            </table>

            <p class="footer">Sistem Pemesanan Restoran &mdash; Generated by Dompdf</p>
        </body>
        </html>
        HTML;
    }

    /**
     * Format tanggal ke format Indonesia.
     *
     * @param string $datetime
     * @return string
     */
    private function formatTanggalIndo(string $datetime): string
    {
        return date('d/m/Y H:i', strtotime($datetime));
    }
}
