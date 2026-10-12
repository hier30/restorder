<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Class Meja — merepresentasikan meja di restoran.
 *
 * Encapsulation: semua property private, diakses lewat getter/setter.
 *
 * @package App\Models
 */
class Meja
{
    /** @var int|null ID meja */
    private ?int $id;

    /** @var string Nomor meja (misal: M01) */
    private string $nomorMeja;

    /** @var int Kapasitas kursi */
    private int $kapasitas;

    /** @var string Status: tersedia, terisi, dipesan */
    private string $status;

    public function __construct(
        string $nomorMeja = '',
        int $kapasitas = 4,
        string $status = 'tersedia',
        ?int $id = null
    ) {
        $this->nomorMeja = $nomorMeja;
        $this->kapasitas = $kapasitas;
        $this->status    = $status;
        $this->id        = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getNomorMeja(): string
    {
        return $this->nomorMeja;
    }

    public function setNomorMeja(string $nomorMeja): void
    {
        $this->nomorMeja = $nomorMeja;
    }

    public function getKapasitas(): int
    {
        return $this->kapasitas;
    }

    public function setKapasitas(int $kapasitas): void
    {
        $this->kapasitas = $kapasitas;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * Cek apakah meja tersedia untuk reservasi.
     *
     * @return bool true jika status 'tersedia'
     */
    public function isTersedia(): bool
    {
        return $this->status === 'tersedia';
    }
}
