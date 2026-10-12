<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\MenuRepository;
use App\Repositories\MejaRepository;
use App\Repositories\PelangganRepository;
use App\Repositories\PesananRepository;
use App\Services\PesananService;
use App\Services\ValidasiService;
use InvalidArgumentException;
use PDOException;

/**
 * Controller untuk halaman pesanan.
 *
 * @package App\Controllers
 */
class PesananController extends BaseController
{
    private PesananRepository $pesananRepo;
    private PesananService $pesananService;
    private MenuRepository $menuRepo;
    private MejaRepository $mejaRepo;
    private PelangganRepository $pelangganRepo;

    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
        $this->pesananRepo   = new PesananRepository($pdo);
        $this->menuRepo      = new MenuRepository($pdo);
        $this->mejaRepo      = new MejaRepository($pdo);
        $this->pelangganRepo = new PelangganRepository($pdo);
        $this->pesananService = new PesananService(
            $this->pesananRepo,
            $this->menuRepo,
            $this->mejaRepo,
            new ValidasiService()
        );
    }

    public function index(): void
    {
        $pesananList = $this->pesananRepo->findAll();
        $this->render('pesanan/index.php', [
            'pageTitle'    => 'Daftar Pesanan',
            'currentPage'  => 'pesanan',
            'pesananList'  => $pesananList,
        ]);
    }

    public function create(): void
    {
        $this->render('pesanan/form.php', [
            'pageTitle'      => 'Buat Pesanan Baru',
            'currentPage'    => 'pesanan',
            'pelangganList'  => $this->pelangganRepo->findAll(),
            'menuList'       => $this->menuRepo->findTersedia(),
            'mejaTersedia'   => $this->mejaRepo->findTersedia(),
        ]);
    }

    public function store(): void
    {
        try {
            $items = [];
            $menuIds = $_POST['menu_id'] ?? [];
            $jumlahs = $_POST['jumlah'] ?? [];

            for ($i = 0; $i < count($menuIds); $i++) {
                if (!empty($menuIds[$i]) && !empty($jumlahs[$i])) {
                    $items[] = [
                        'menu_id' => (int) $menuIds[$i],
                        'jumlah'  => (int) $jumlahs[$i],
                    ];
                }
            }

            $data = [
                'pelanggan_id' => $_POST['pelanggan_id'] ?? '',
                'tipe_pesanan' => $_POST['tipe_pesanan'] ?? '',
                'meja_id'      => $_POST['meja_id'] ?? null,
                'catatan'      => $_POST['catatan'] ?? '',
                'items'        => $items,
            ];

            $pesananId = $this->pesananService->buatPesanan($data);
            $this->setFlash("Pesanan #{$pesananId} berhasil dibuat.");
        } catch (InvalidArgumentException $e) {
            $this->setFlash($e->getMessage(), 'danger');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->setFlash('Gagal menyimpan pesanan.', 'danger');
        }
        $this->redirect('?page=pesanan');
    }

    public function detail(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $pesanan = $this->pesananRepo->findById($id);

        if ($pesanan === null) {
            $this->setFlash('Pesanan tidak ditemukan.', 'danger');
            $this->redirect('?page=pesanan');
            return;
        }

        $this->render('pesanan/detail.php', [
            'pageTitle'   => 'Detail Pesanan #' . $id,
            'currentPage' => 'pesanan',
            'pesanan'     => $pesanan,
        ]);
    }

    public function proses(): void
    {
        $id = $this->ambilIdPost('?page=pesanan');
        try {
            $this->pesananService->prosesPesanan($id);
            $this->setFlash("Pesanan #{$id} sedang diproses.");
        } catch (InvalidArgumentException $e) {
            $this->setFlash($e->getMessage(), 'danger');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->setFlash('Gagal memproses pesanan.', 'danger');
        }
        $this->redirect('?page=pesanan');
    }

    public function selesai(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        try {
            $this->pesananService->selesaikanPesanan($id);
            $this->setFlash("Pesanan #{$id} telah diselesaikan.");
        } catch (InvalidArgumentException $e) {
            $this->setFlash($e->getMessage(), 'danger');
        }
        $this->redirect('?page=pesanan');
    }

    public function batal(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        try {
            $this->pesananService->batalkanPesanan($id);
            $this->setFlash("Pesanan #{$id} telah dibatalkan.");
        } catch (InvalidArgumentException $e) {
            $this->setFlash($e->getMessage(), 'danger');
        }
        $this->redirect('?page=pesanan');
    }

    public function delete(): void
    {
        $id = $this->ambilIdPost('?page=pesanan');
        try {
            $this->pesananRepo->delete($id);
            $this->setFlash("Pesanan #{$id} berhasil dihapus.");
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->setFlash('Gagal menghapus pesanan.', 'danger');
        }
        $this->redirect('?page=pesanan');
    }
}
