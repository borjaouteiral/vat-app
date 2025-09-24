<?php
namespace Src\Infrastructure\Persistence;

use Src\Domain\Entity\Vat;
use Src\Domain\Repository\VatRepositoryInterface;
use PDO;

class VatRepository implements VatRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Vat $vat): bool
    {
        if ($vat->getId() !== null) {
            $stmt = $this->connection->prepare("
                INSERT INTO vat (id, number, created_at) 
                VALUES (:id, :number, :created_at)
            ");
            return $stmt->execute([
                'id' => $vat->getId(),
                'number' => $vat->getNumber(),
                'created_at' => $vat->getCreatedAt()->format('Y-m-d H:i:s')
            ]);
        } else {
            // Autoincrement
            $stmt = $this->connection->prepare("
                INSERT INTO vat (number, created_at) 
                VALUES (:number, :created_at)
            ");
            return $stmt->execute([
                'number' => $vat->getNumber(),
                'created_at' => $vat->getCreatedAt()->format('Y-m-d H:i:s')
            ]);
        }
    }


    public function logVat(?int $vatId, string $vatNumber, string $status): void
    {
        $stmt = $this->connection->prepare("
            INSERT INTO vat_log (vat_id, vat_number, status, created_at)
            VALUES (:vat_id, :vat_number, :status, NOW())
        ");
        $stmt->execute([
            'vat_id' => $vatId,
            'vat_number' => $vatNumber,
            'status' => $status
        ]);
    }

    public function findAllByStatus(string $status): array
    {
        $stmt = $this->connection->prepare("SELECT * FROM vat_log WHERE status = :status");
        $stmt->execute(['status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existsByVatNumber(string $vatNumber): bool
    {
        $stmt = $this->connection->prepare("SELECT COUNT(*) FROM vat WHERE number = :number");
        $stmt->execute(['number' => $vatNumber]);
        return $stmt->fetchColumn() > 0;
    }

    public function existsById(int $id): bool
    {
        $stmt = $this->connection->prepare("SELECT COUNT(*) FROM vat WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchColumn() > 0;
    }

}