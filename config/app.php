<?php

/**
 * Konfigurasi umum aplikasi.
 *
 * Mengatur error reporting berdasarkan APP_DEBUG di file .env.
 * Jika APP_DEBUG=true, error ditampilkan di browser (untuk development).
 * Jika APP_DEBUG=false, error disembunyikan dan dicatat ke log (untuk production).
 */

declare(strict_types=1);

$appDebug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';

if ($appDebug) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL);
    ini_set('log_errors', '1');
    ini_set('error_log', dirname(__DIR__) . '/logs/app.log');
}

$appName = $_ENV['APP_NAME'] ?? 'Sistem Pemesanan Restoran';
$appUrl  = $_ENV['APP_URL'] ?? 'http://localhost:8000';
