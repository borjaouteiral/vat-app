<?php
use Src\Presentation\Controller\VatController;

return [
    '/' => [VatController::class, 'showHome'],
    'upload-csv' => [VatController::class, 'uploadCsv'],
    'manual-vat' => [VatController::class, 'manualVat'],
];
