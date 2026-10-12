<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Class Admin — turunan dari Pengguna.
 *
 * Mendemonstrasikan INHERITANCE: mewarisi dari Pengguna,
 * menambahkan property role (peran admin).
 *
 * @package App\Models
 */
class Admin extends Pengguna
{
    /** @var string Peran/jabatan admin */
    private string $role;

    /**
     * Constructor Admin.
     *
     * @param string      $nama   Nama admin
     * @param string|null $kontak Nomor telepon
     * @param int|null    $id     ID database
     * @param string      $role   Peran admin (misal: 'kasir', 'manajer')
     */
    public function __construct(
        string $nama = '',
        ?string $kontak = null,
        ?int $id = null,
        string $role = 'kasir'
    ) {
        parent::__construct($nama, $kontak, $id);
        $this->role = $role;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    /**
     * Implementasi method abstract dari Pengguna (polymorphism).
     *
     * @return string Info admin
     */
    public function getInfo(): string
    {
        return "Admin: {$this->nama} | Role: {$this->role} | Telp: {$this->kontak}";
    }
}
