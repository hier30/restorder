<?php

/**
 * Entry point utama aplikasi.
 *
 * Semua request diarahkan ke file ini. File ini memuat autoloader Composer,
 * konfigurasi database & aplikasi, lalu merutekan request ke controller yang sesuai.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$pdo = require __DIR__ . '/../config/database.php';
require __DIR__ . '/../config/app.php';

session_start();

$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

$routes = [
    'dashboard'  => App\Controllers\DashboardController::class,
    'meja'       => App\Controllers\MejaController::class,
    'pelanggan'  => App\Controllers\PelangganController::class,
    'menu'       => App\Controllers\MenuController::class,
    'pesanan'    => App\Controllers\PesananController::class,
    'laporan'    => App\Controllers\LaporanController::class,
];

try {
    if (!isset($routes[$page])) {
        http_response_code(404);
        echo "Halaman tidak ditemukan.";
        exit;
    }

    $controllerClass = $routes[$page];
    $controller = new $controllerClass($pdo);

    if (!method_exists($controller, $action)) {
        http_response_code(404);
        echo "Action tidak ditemukan.";
        exit;
    }

    $controller->$action();
} catch (\Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo "Terjadi kesalahan pada server. Silakan coba lagi.";
}
