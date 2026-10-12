<?php

/**
 * Konfigurasi koneksi database.
 *
 * File ini membaca kredensial dari file .env menggunakan library vlucas/phpdotenv.
 * Jangan hardcode username/password di sini — cukup salin .env.example menjadi .env
 * lalu isi sesuai environment lokal Anda.
 *
 * @return PDO Instance koneksi PDO ke database MySQL/MariaDB
 * @throws PDOException Jika koneksi gagal
 */

declare(strict_types=1);

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$host     = $_ENV['DB_HOST'] ?? 'localhost';
$port     = $_ENV['DB_PORT'] ?? '3306';
$database = $_ENV['DB_DATABASE'] ?? 'restorder';
$username = $_ENV['DB_USERNAME'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? '';

$dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    throw new PDOException("Koneksi database gagal. Periksa konfigurasi .env Anda.");
}

return $pdo;
