<?php
namespace Src\Domain\Entity;

class Vat
{
    private ?int $id;
    private string $number;
    private \DateTimeImmutable $createdAt;

    public function __construct(?int $id, string $number, ?\DateTimeImmutable $createdAt = null)
    {
        $this->id = $id;
        $this->number = $number;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getNumber(): string { return $this->number; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
