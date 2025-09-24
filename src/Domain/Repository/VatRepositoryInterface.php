<?php
namespace Src\Domain\Repository;

use Src\Domain\Entity\Vat;

interface VatRepositoryInterface
{
    public function save(Vat $vat): bool;
    public function logVat(int $vatId, string $vatNumber, string $status): void;
    public function findAllByStatus(string $status): array;
    public function existsByVatNumber(string $vatNumber): bool;
    public function existsById(int $id): bool;
    
}