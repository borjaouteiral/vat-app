<?php
namespace Src\Application\Service;

use Src\Domain\Repository\VatRepositoryInterface;
use Src\Domain\Entity\Vat;

class ProcessSingleVatService
{
    private VatRepositoryInterface $repository;

    public function __construct(VatRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function validateAndSaveManualVat(string $vatNumber): array
    {
        $vatNumber = strtoupper(trim($vatNumber));

        // Basic validation
        if (!preg_match('/^IT\d{11}$/', $vatNumber)) {
            return [
                'success' => false,
                'message' => 'The VAT number must start with IT followed by 11 digits.'
            ];
        }

        // Check for duplicates
        if ($this->repository->existsByVatNumber($vatNumber)) {
            return [
                'success' => false,
                'message' => 'Duplicate VAT number, already exists in the database.'
            ];
        }

        // Save to the database
        $vat = new Vat(null, $vatNumber); 
        $this->repository->save($vat);

        return [
            'success' => true,
            'message' => 'VAT number successfully inserted.'
        ];
    }
}