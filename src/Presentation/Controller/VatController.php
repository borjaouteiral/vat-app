<?php
namespace Src\Presentation\Controller;

use Src\Infrastructure\FileUpload\CsvVatReader;
use Src\Application\Service\ProcessVatFileService;
use Src\Application\Service\ProcessSingleVatService;

class VatController
{
    private ProcessVatFileService $processVatFileService;
    private ProcessSingleVatService $vatService;
    private CsvVatReader $csvReader;

    public function __construct(ProcessVatFileService $processVatFileService, CsvVatReader $csvReader, ProcessSingleVatService $vatService)
    {
        $this->processVatFileService = $processVatFileService;
        $this->csvReader = $csvReader;
        $this->vatService = $vatService;
    }

    public function showHome()
    {
    include __DIR__ . '/../View/form.php';
    }

    public function uploadCsv(array $file)
    {
        $filePath = $file['tmp_name'];
        $vatNumbers = $this->csvReader->readFile($filePath);

        if (isset($vatNumbers['error']) && $vatNumbers['error'] === true) {
            return '<div class="alert alert-danger">' . htmlspecialchars($vatNumbers['message']) . '</div>';
        } else {
            return $this->processVatFileService->process($vatNumbers);
        }        
    }

    public function manualVat()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $vatNumber = $input['vat_number'] ?? null;

        if (!$vatNumber) {
            echo json_encode(['success' => false, 'message' => 'VAT number is required']);
            return;
        }

        $result = $this->vatService->validateAndSaveManualVat($vatNumber);

        if ($result['success']) {
            echo json_encode(['success' => true, 'message' => $result['message']]);
        } else {
            echo json_encode(['success' => false, 'message' => $result['message']]);
        }
    }
}