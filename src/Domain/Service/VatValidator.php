<?php
namespace Src\Domain\Service;

use Src\Domain\ValueObject\VatNumber;

class VatValidator
{
    public const STATUS_VALID = 'valid';
    public const STATUS_FIXED = 'fixed';
    public const STATUS_INVALID = 'invalid';
    public const STATUS_DUPLICATE = 'duplicate';
    public const STATUS_REPLACE = 'replace';

    public function validate(VatNumber $vatNumber): array
    {
        $raw = $vatNumber->getValue();

        if (preg_match('/^IT\d{11}$/', $raw)) {
            return ['status' => self::STATUS_VALID, 'corrected' => null];
        }

        if (preg_match('/^\d{11}$/', $raw)) {
            return ['status' => self::STATUS_FIXED, 'corrected' => "IT{$raw}"];
        }

        return ['status' => self::STATUS_INVALID, 'corrected' => null];
    }
}
?>