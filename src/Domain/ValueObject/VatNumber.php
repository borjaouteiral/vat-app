<?php
namespace Src\Domain\ValueObject;

class VatNumber
{
    private string $raw;

    public function __construct(string $raw)
    {
        $this->raw = trim($raw);
    }

    public function getValue(): string
    {
        return $this->raw;
    }
}