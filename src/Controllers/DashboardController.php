<?php

declare(strict_types=1);

namespace App\Controllers;

/**
 * Controller untuk halaman dashboard.
 *
 * @package App\Controllers
 */
class DashboardController extends BaseController
{
    /**
     * Tampilkan halaman dashboard dengan ringkasan data.
     */
    public function index(): void
    {
        $totalMeja = $this->pdo->query("SELECT COUNT(*) FROM meja")->fetchColumn();
        $mejaTersedia = $this->pdo->query(
            "SELECT COUNT(*) FROM meja WHERE status = 'tersedia'"
        )->fetchColumn();
        $totalPelanggan = $this->pdo->query("SELECT COUNT(*) FROM pelanggan")->fetchColumn();
        $totalMenu = $this->pdo->query("SELECT COUNT(*) FROM menu WHERE tersedia = 1")->fetchColumn();
        $totalPesanan = $this->pdo->query("SELECT COUNT(*) FROM pesanan")->fetchColumn();
        $pesananHariIni = $this->pdo->query(
            "SELECT COUNT(*) FROM pesanan WHERE DATE(tanggal_pesanan) = CURDATE()"
        )->fetchColumn();
        $pendapatanHariIni = $this->pdo->query(
            "SELECT COALESCE(SUM(total), 0) FROM pesanan
             WHERE DATE(tanggal_pesanan) = CURDATE() AND status_pesanan != 'dibatalkan'"
        )->fetchColumn();

        $pesananTerbaru = $this->pdo->query(
            "SELECT p.*, pel.nama AS nama_pelanggan
             FROM pesanan p
             LEFT JOIN pelanggan pel ON p.pelanggan_id = pel.id
             ORDER BY p.tanggal_pesanan DESC LIMIT 5"
        )->fetchAll();

        $this->render('dashboard/index.php', [
            'pageTitle'        => 'Dashboard - Sistem Pemesanan Restoran',
            'currentPage'      => 'dashboard',
            'totalMeja'        => $totalMeja,
            'mejaTersedia'     => $mejaTersedia,
            'totalPelanggan'   => $totalPelanggan,
            'totalMenu'        => $totalMenu,
            'totalPesanan'     => $totalPesanan,
            'pesananHariIni'   => $pesananHariIni,
            'pendapatanHariIni' => $pendapatanHariIni,
            'pesananTerbaru'   => $pesananTerbaru,
        ]);
    }
}
