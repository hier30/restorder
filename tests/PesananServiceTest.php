<?php

declare(strict_types=1);

namespace Tests;

use App\Repositories\MejaRepository;
use App\Repositories\MenuRepository;
use App\Repositories\PesananRepository;
use App\Services\PesananService;
use App\Services\ValidasiService;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test untuk PesananService::prosesPesanan().
 *
 * Repository di-mock (objek tiruan) sehingga test tidak butuh database.
 */
class PesananServiceTest extends TestCase
{
    private PesananRepository&MockObject $pesananRepo;
    private PesananService $service;

    protected function setUp(): void
    {
        $this->pesananRepo = $this->createMock(PesananRepository::class);
        $this->service = new PesananService(
            $this->pesananRepo,
            $this->createMock(MenuRepository::class),
            $this->createMock(MejaRepository::class),
            new ValidasiService()
        );
    }

    /**
     * Kasus normal: pesanan 'pending' berubah menjadi 'diproses'.
     */
    public function testProsesPesananPendingBerhasil(): void
    {
        $this->pesananRepo->method('findById')
            ->with(7)
            ->willReturn(['id' => 7, 'status_pesanan' => 'pending']);

        $this->pesananRepo->expects($this->once())
            ->method('updateStatus')
            ->with(7, 'diproses')
            ->willReturn(true);

        $this->assertTrue($this->service->prosesPesanan(7));
    }

    /**
     * Edge: pesanan tidak ada → exception, status tidak diubah.
     */
    public function testProsesPesananTidakDitemukan(): void
    {
        $this->pesananRepo->method('findById')->willReturn(null);
        $this->pesananRepo->expects($this->never())->method('updateStatus');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('tidak ditemukan');

        $this->service->prosesPesanan(999);
    }

    /**
     * Edge: hanya 'pending' yang boleh diproses; status lain ditolak.
     *
     * @dataProvider statusSelainPendingProvider
     */
    public function testProsesPesananStatusSelainPendingDitolak(string $status): void
    {
        $this->pesananRepo->method('findById')
            ->willReturn(['id' => 3, 'status_pesanan' => $status]);
        $this->pesananRepo->expects($this->never())->method('updateStatus');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('tidak bisa diproses');

        $this->service->prosesPesanan(3);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function statusSelainPendingProvider(): array
    {
        return [
            'sudah diproses' => ['diproses'],
            'sudah selesai'  => ['selesai'],
            'dibatalkan'     => ['dibatalkan'],
        ];
    }
}
