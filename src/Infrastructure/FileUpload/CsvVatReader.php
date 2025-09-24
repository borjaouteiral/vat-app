<?php
namespace Src\Infrastructure\FileUpload;

use Exception;

class CsvVatReader
{
    public function readFile(string $filePath): array
    {
        try {
            $vatNumbers = [];

            if (!file_exists($filePath)) {
                throw new Exception("The file does not exist: {$filePath}");
            }

            if (($handle = fopen($filePath, "r")) === false) {
                throw new Exception("Unable to open the file: {$filePath}");
            }

            // Read header
            $header = fgetcsv($handle);
            if ($header === false) {
                throw new Exception("The CSV file is empty.");
            }

            $normalizedHeader = array_map('strtolower', $header);

            if (!in_array('vat_number', $normalizedHeader, true)) {
                throw new Exception("The CSV file must contain the column 'vat_number'.");
            }

            $vatIndex = array_search('vat_number', $normalizedHeader);
            $idIndex = array_search('id', $normalizedHeader); // May be false if not present

            while (($data = fgetcsv($handle)) !== false) {
                // Ignore empty rows
                if (empty($data[$vatIndex])) {
                    continue;
                }

                $row = [
                    'vat_number' => trim($data[$vatIndex])
                ];

                if ($idIndex !== false && isset($data[$idIndex]) && trim($data[$idIndex]) !== '') {
                    $row['id'] = trim($data[$idIndex]);
                } else {
                    $row['id'] = null;
                }

                $vatNumbers[] = $row;
            }

            fclose($handle);

            if (empty($vatNumbers)) {
                throw new Exception("The CSV file does not contain any valid data rows.");
            }

            return $vatNumbers;
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => 'There was a problem processing the file. '.$e->getMessage()
            ];
        }
    }
}
