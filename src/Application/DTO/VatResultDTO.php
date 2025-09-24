<?php
namespace Src\Application\DTO;

class VatResultDTO
{
    public string $original;
    public string $status;
    public ?string $corrected;

    public function __construct(string $original, string $status, ?string $corrected = null)
    {
        $this->original = $original;
        $this->status = $status;
        $this->corrected = $corrected;
    }
}