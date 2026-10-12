<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Menu;
use App\Repositories\MenuRepository;
use App\Services\ValidasiService;
use InvalidArgumentException;
use PDOException;

/**
 * Controller untuk CRUD data menu restoran.
 *
 * @package App\Controllers
 */
class MenuController extends BaseController
{
    private MenuRepository $repo;
    private ValidasiService $validasi;

    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
        $this->repo = new MenuRepository($pdo);
        $this->validasi = new ValidasiService();
    }

    public function index(): void
    {
        $menuList = $this->repo->findAll();
        $this->render('menu/index.php', [
            'pageTitle'   => 'Daftar Menu',
            'currentPage' => 'menu',
            'menuList'    => $menuList,
        ]);
    }

    public function create(): void
    {
        $this->render('menu/form.php', [
            'pageTitle'   => 'Tambah Menu',
            'currentPage' => 'menu',
            'menu'        => null,
            'formAction'  => '?page=menu&action=store',
        ]);
    }

    public function store(): void
    {
        try {
            $data = $this->validasi->validasiMenu($_POST);
            $menu = new Menu(
                $data['nama_menu'], $data['kategori'], $data['harga'],
                $data['deskripsi'], $data['tersedia']
            );
            $this->repo->create($menu);
            $this->setFlash('Menu berhasil ditambahkan.');
        } catch (InvalidArgumentException $e) {
            $this->setFlash($e->getMessage(), 'danger');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->setFlash('Gagal menyimpan menu.', 'danger');
        }
        $this->redirect('?page=menu');
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $menu = $this->repo->findById($id);

        if ($menu === null) {
            $this->setFlash('Menu tidak ditemukan.', 'danger');
            $this->redirect('?page=menu');
            return;
        }

        $this->render('menu/form.php', [
            'pageTitle'   => 'Edit Menu',
            'currentPage' => 'menu',
            'menu'        => $menu,
            'formAction'  => '?page=menu&action=update&id=' . $id,
        ]);
    }

    public function update(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        try {
            $data = $this->validasi->validasiMenu($_POST);
            $menu = new Menu(
                $data['nama_menu'], $data['kategori'], $data['harga'],
                $data['deskripsi'], $data['tersedia'], $id
            );
            $this->repo->update($menu);
            $this->setFlash('Menu berhasil diperbarui.');
        } catch (InvalidArgumentException $e) {
            $this->setFlash($e->getMessage(), 'danger');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->setFlash('Gagal memperbarui menu.', 'danger');
        }
        $this->redirect('?page=menu');
    }

    public function delete(): void
    {
        $id = $this->ambilIdPost('?page=menu');
        try {
            $this->repo->delete($id);
            $this->setFlash('Menu berhasil dihapus.');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->setFlash('Gagal menghapus menu. Mungkin masih ada pesanan terkait.', 'danger');
        }
        $this->redirect('?page=menu');
    }
}
