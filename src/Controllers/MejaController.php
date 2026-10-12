<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Meja;
use App\Repositories\MejaRepository;
use App\Services\ValidasiService;
use InvalidArgumentException;
use PDOException;

/**
 * Controller untuk halaman kelola meja & reservasi.
 *
 * @package App\Controllers
 */
class MejaController extends BaseController
{
    private MejaRepository $repo;
    private ValidasiService $validasi;

    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
        $this->repo = new MejaRepository($pdo);
        $this->validasi = new ValidasiService();
    }

    public function index(): void
    {
        $mejaList = $this->repo->findAll();
        $this->render('meja/index.php', [
            'pageTitle'   => 'Kelola Meja',
            'currentPage' => 'meja',
            'mejaList'    => $mejaList,
        ]);
    }

    public function create(): void
    {
        $this->render('meja/form.php', [
            'pageTitle'   => 'Tambah Meja',
            'currentPage' => 'meja',
            'meja'        => null,
            'formAction'  => '?page=meja&action=store',
        ]);
    }

    public function store(): void
    {
        try {
            $data = $this->validasi->validasiMeja($_POST);
            $meja = new Meja($data['nomor_meja'], $data['kapasitas'], $data['status']);
            $this->repo->create($meja);
            $this->setFlash('Meja berhasil ditambahkan.');
        } catch (InvalidArgumentException $e) {
            $this->setFlash($e->getMessage(), 'danger');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->setFlash('Gagal menyimpan meja. Nomor meja mungkin sudah ada.', 'danger');
        }
        $this->redirect('?page=meja');
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $meja = $this->repo->findById($id);

        if ($meja === null) {
            $this->setFlash('Meja tidak ditemukan.', 'danger');
            $this->redirect('?page=meja');
            return;
        }

        $this->render('meja/form.php', [
            'pageTitle'   => 'Edit Meja',
            'currentPage' => 'meja',
            'meja'        => $meja,
            'formAction'  => '?page=meja&action=update&id=' . $id,
        ]);
    }

    public function update(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        try {
            $data = $this->validasi->validasiMeja($_POST);
            $meja = new Meja($data['nomor_meja'], $data['kapasitas'], $data['status'], $id);
            $this->repo->update($meja);
            $this->setFlash('Meja berhasil diperbarui.');
        } catch (InvalidArgumentException $e) {
            $this->setFlash($e->getMessage(), 'danger');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->setFlash('Gagal memperbarui meja.', 'danger');
        }
        $this->redirect('?page=meja');
    }

    public function delete(): void
    {
        $id = $this->ambilIdPost('?page=meja');
        try {
            $this->repo->delete($id);
            $this->setFlash('Meja berhasil dihapus.');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->setFlash('Gagal menghapus meja. Mungkin masih ada pesanan terkait.', 'danger');
        }
        $this->redirect('?page=meja');
    }

    public function reservasi(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        try {
            $this->repo->updateStatus($id, 'dipesan');
            $this->setFlash('Meja berhasil dipesan/direservasi.');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->setFlash('Gagal mereservasi meja.', 'danger');
        }
        $this->redirect('?page=meja');
    }

    public function bebaskan(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        try {
            $this->repo->updateStatus($id, 'tersedia');
            $this->setFlash('Status meja dikembalikan ke tersedia.');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->setFlash('Gagal membebaskan meja.', 'danger');
        }
        $this->redirect('?page=meja');
    }
}
