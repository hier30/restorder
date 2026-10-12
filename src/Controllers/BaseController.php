<?php

declare(strict_types=1);

namespace App\Controllers;

use PDO;

/**
 * Base controller — menyediakan method utilitas yang dipakai semua controller.
 *
 * @package App\Controllers
 */
class BaseController
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Render view dengan layout.
     *
     * @param string $viewFile Path file view (relatif dari folder views/)
     * @param array  $data     Data yang dikirim ke view
     */
    protected function render(string $viewFile, array $data = []): void
    {
        // Extract data menjadi variabel terpisah agar bisa dipakai di view
        extract($data);

        // Cek flash message dari session
        $flashMessage = $_SESSION['flash_message'] ?? null;
        $flashType    = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);

        // Render view ke buffer
        ob_start();
        $viewPath = __DIR__ . '/../../views/' . $viewFile;
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "<p class='text-danger'>View tidak ditemukan: {$viewFile}</p>";
        }
        $content = ob_get_clean();

        // Render layout utama
        require __DIR__ . '/../../views/layouts/main.php';
    }

    /**
     * Simpan flash message ke session.
     *
     * @param string $message Pesan
     * @param string $type    Tipe Bootstrap alert (success, danger, warning, info)
     */
    protected function setFlash(string $message, string $type = 'success'): void
    {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type']    = $type;
    }

    /**
     * Ambil ID dari request POST untuk aksi yang mengubah data (misal hapus).
     *
     * Request selain POST (misal membuka URL langsung lewat GET) ditolak
     * dan di-redirect, agar data tidak berubah hanya karena membuka link.
     *
     * @param string $kembaliKe URL tujuan redirect jika request bukan POST
     * @return int ID dari $_POST['id'] (0 jika tidak ada)
     */
    protected function ambilIdPost(string $kembaliKe): int
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->setFlash('Aksi ini hanya bisa dilakukan lewat tombol (metode POST).', 'danger');
            $this->redirect($kembaliKe);
        }

        return (int) ($_POST['id'] ?? 0);
    }

    /**
     * Redirect ke URL lain.
     *
     * @param string $url
     */
    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}
