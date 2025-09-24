<?php
namespace Src\Application\Service;

use Src\Domain\Repository\VatRepositoryInterface;
use Src\Domain\Service\VatValidator;
use Src\Domain\Entity\Vat;
use Src\Domain\ValueObject\VatNumber;
use Src\Application\DTO\VatResultDTO;

class ProcessVatFileService
{
    private VatRepositoryInterface $repository;
    private VatValidator $validator;

    public function __construct(VatRepositoryInterface $repository, VatValidator $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    public function process(array $vatData): array
    {
        $results = [];
        $statusSummary = [];

        foreach ($vatData as $row) {
            $rawVat = $row['vat_number'];
            $csvId = $row['id'] ?? null;

            $vatNumberVO = new VatNumber($rawVat);
            $validation = $this->validator->validate($vatNumberVO);

            $status = $validation['status'];
            $corrected = $validation['corrected'] ?? null;
            $finalVatNumber = $corrected ?? $rawVat;

            $vatIdForTable = null;

            $isDuplicate = $this->repository->existsByVatNumber($finalVatNumber);

            if ($isDuplicate) {
                $status = 'duplicate';
            }

            if ($csvId !== null && is_numeric($csvId) && in_array($status, ['valid', 'fixed'], true)) {
                $idExists = $this->repository->existsById((int)$csvId);
                if ($idExists) {
                    $status = 'replace';
                    $vatIdForLog = (int)$csvId;
                    $vatIdForTable = null;
                } else {
                    $vatIdForLog = (int)$csvId;
                    $vatIdForTable = (int)$csvId;
                }
            } else {
                $vatIdForLog = null;
            }

            $this->repository->logVat($vatIdForLog, $rawVat, $status);

            if (in_array($status, ['valid', 'fixed','replace'], true)) {
                $vat = new Vat($vatIdForTable, $finalVatNumber); 
                $this->repository->save($vat);
            }

            $results[] = new VatResultDTO(
                $rawVat,
                $status,
                $corrected
            );

            if (!isset($statusSummary[$status])) {
                $statusSummary[$status] = 0;
            }
            $statusSummary[$status]++;
        }

        $results['summary'] = $statusSummary;

        return $results;
    }

}
