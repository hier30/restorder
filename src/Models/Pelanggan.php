<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Class Pelanggan — turunan dari Pengguna.
 *
 * Mendemonstrasikan INHERITANCE: mewarisi property dan method dari Pengguna,
 * lalu menambahkan property spesifik pelanggan (email, alamat).
 *
 * @package App\Models
 */
class Pelanggan extends Pengguna
{
    /** @var string|null Alamat email pelanggan */
    private ?string $email;

    /** @var string|null Alamat rumah/kantor pelanggan */
    private ?string $alamat;

    /**
     * Constructor Pelanggan.
     *
     * Emulasi overloading: parameter opsional memungkinkan pemanggilan fleksibel.
     *
     * @param string      $nama   Nama pelanggan
     * @param string|null $kontak Nomor telepon
     * @param int|null    $id     ID database
     * @param string|null $email  Alamat email
     * @param string|null $alamat Alamat rumah
     */
    public function __construct(
        string $nama = '',
        ?string $kontak = null,
        ?int $id = null,
        ?string $email = null,
        ?string $alamat = null
    ) {
        parent::__construct($nama, $kontak, $id);
        $this->email  = $email;
        $this->alamat = $alamat;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getAlamat(): ?string
    {
        return $this->alamat;
    }

    public function setAlamat(?string $alamat): void
    {
        $this->alamat = $alamat;
    }

    /**
     * Implementasi method abstract dari Pengguna (polymorphism).
     *
     * @return string Info pelanggan
     */
    public function getInfo(): string
    {
        return "Pelanggan: {$this->nama} | Telp: {$this->kontak} | Email: {$this->email}";
    }
}
