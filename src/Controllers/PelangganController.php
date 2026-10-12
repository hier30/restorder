<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Pelanggan;
use App\Repositories\PelangganRepository;
use App\Services\ValidasiService;
use InvalidArgumentException;
use PDOException;

/**
 * Controller untuk CRUD data pelanggan.
 *
 * @package App\Controllers
 */
class PelangganController extends BaseController
{
    private PelangganRepository $repo;
    private ValidasiService $validasi;

    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
        $this->repo = new PelangganRepository($pdo);
        $this->validasi = new ValidasiService();
    }

    public function index(): void
    {
        $pelangganList = $this->repo->findAll();
        $this->render('pelanggan/index.php', [
            'pageTitle'      => 'Data Pelanggan',
            'currentPage'    => 'pelanggan',
            'pelangganList'  => $pelangganList,
        ]);
    }

    public function create(): void
    {
        $this->render('pelanggan/form.php', [
            'pageTitle'   => 'Tambah Pelanggan',
            'currentPage' => 'pelanggan',
            'pelanggan'   => null,
            'formAction'  => '?page=pelanggan&action=store',
        ]);
    }

    public function store(): void
    {
        try {
            $data = $this->validasi->validasiPelanggan($_POST);
            $pelanggan = new Pelanggan(
                $data['nama'], $data['no_telepon'], null,
                $data['email'], $data['alamat']
            );
            $this->repo->create($pelanggan);
            $this->setFlash('Pelanggan berhasil ditambahkan.');
        } catch (InvalidArgumentException $e) {
            $this->setFlash($e->getMessage(), 'danger');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->setFlash('Gagal menyimpan data pelanggan.', 'danger');
        }
        $this->redirect('?page=pelanggan');
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $pelanggan = $this->repo->findById($id);

        if ($pelanggan === null) {
            $this->setFlash('Pelanggan tidak ditemukan.', 'danger');
            $this->redirect('?page=pelanggan');
            return;
        }

        $this->render('pelanggan/form.php', [
            'pageTitle'   => 'Edit Pelanggan',
            'currentPage' => 'pelanggan',
            'pelanggan'   => $pelanggan,
            'formAction'  => '?page=pelanggan&action=update&id=' . $id,
        ]);
    }

    public function update(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        try {
            $data = $this->validasi->validasiPelanggan($_POST);
            $pelanggan = new Pelanggan(
                $data['nama'], $data['no_telepon'], $id,
                $data['email'], $data['alamat']
            );
            $this->repo->update($pelanggan);
            $this->setFlash('Pelanggan berhasil diperbarui.');
        } catch (InvalidArgumentException $e) {
            $this->setFlash($e->getMessage(), 'danger');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->setFlash('Gagal memperbarui data pelanggan.', 'danger');
        }
        $this->redirect('?page=pelanggan');
    }

    public function delete(): void
    {
        $id = $this->ambilIdPost('?page=pelanggan');
        try {
            $this->repo->delete($id);
            $this->setFlash('Pelanggan berhasil dihapus.');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->setFlash('Gagal menghapus pelanggan. Mungkin masih ada pesanan terkait.', 'danger');
        }
        $this->redirect('?page=pelanggan');
    }
}
