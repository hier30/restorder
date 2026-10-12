<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\PelangganRepository;
use App\Repositories\PesananRepository;
use App\Services\LaporanService;

/**
 * Controller untuk halaman laporan pemesanan.
 *
 * @package App\Controllers
 */
class LaporanController extends BaseController
{
    private LaporanService $laporanService;

    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
        $this->laporanService = new LaporanService(
            new PesananRepository($pdo),
            new PelangganRepository($pdo)
        );
    }

    /**
     * Tampilkan halaman laporan dengan filter.
     */
    public function index(): void
    {
        $tanggalMulai  = $_GET['tanggal_mulai'] ?? null;
        $tanggalAkhir  = $_GET['tanggal_akhir'] ?? null;
        $pelangganId   = !empty($_GET['pelanggan_id']) ? (int) $_GET['pelanggan_id'] : null;

        $data = $this->laporanService->getDataLaporan(
            $tanggalMulai, $tanggalAkhir, $pelangganId
        );

        $this->render('laporan/index.php', [
            'pageTitle'      => 'Laporan Pemesanan',
            'currentPage'    => 'laporan',
            'pesananList'    => $data['pesanan'],
            'ringkasan'      => $data['ringkasan'],
            'pelangganList'  => $data['pelangganList'],
            'filterMulai'    => $tanggalMulai,
            'filterAkhir'    => $tanggalAkhir,
            'filterPelanggan' => $pelangganId,
        ]);
    }

    /**
     * Export laporan ke PDF menggunakan Dompdf.
     */
    public function exportPdf(): void
    {
        $tanggalMulai  = $_GET['tanggal_mulai'] ?? null;
        $tanggalAkhir  = $_GET['tanggal_akhir'] ?? null;
        $pelangganId   = !empty($_GET['pelanggan_id']) ? (int) $_GET['pelanggan_id'] : null;

        try {
            $this->laporanService->exportPdf($tanggalMulai, $tanggalAkhir, $pelangganId);
        } catch (\Exception $e) {
            error_log("PDF Export Error: " . $e->getMessage());
            $this->setFlash('Gagal mengexport PDF: ' . $e->getMessage(), 'danger');
            $this->redirect('?page=laporan');
        }
    }
}
