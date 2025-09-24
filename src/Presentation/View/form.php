<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use Src\Infrastructure\Persistence\MySQLConnection;
use Src\Infrastructure\Persistence\VatRepository;
use Src\Domain\Service\VatValidator;
use Src\Application\Service\ProcessVatFileService;
use Src\Infrastructure\FileUpload\CsvVatReader;
use Src\Presentation\Controller\VatController;
use Src\Application\Service\ProcessSingleVatService;

$connection = MySQLConnection::getConnection();

$repository = new VatRepository($connection);
$validator = new VatValidator();
$service = new ProcessVatFileService($repository, $validator);
$csvReader = new CsvVatReader();
$singleservice = new ProcessSingleVatService($repository, $validator);

$controller = new VatController($service, $csvReader, $singleservice);

$form = '<div class="vat-actions">
    <form method="POST" enctype="multipart/form-data">
        <label for="csvFile">Upload CSV:</label>
        <input type="file" name="vat_file" id="csvFile" accept=".csv" required>
        <button type="submit">Upload CSV</button>
    </form>

    <hr>

    <div class="manual-validation">
        <div>
            <div class="manual-input">
                <label for="manualVat">Enter VAT manually:</label>
                <input type="text" id="manualVat" placeholder="IT12345678901">
            </div>
            <div class="manual-buttons">  
                <label>&nbsp;</label>  
                <button type="button" onclick="validateVat(false)">Validate VAT</button>
                <button type="button" onclick="validateVat(true)">Validate & Save</button>
            </div>
        </div>    
        <div id="manualVatResult" style="margin-top:8px;"></div>
    </div>
</div>';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/index.css">
    <meta charset="UTF-8">
    <title>VAT</title>
</head>
<body>
<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['vat_file'])) {
    $results = $controller->uploadCsv($_FILES['vat_file']);
    if (is_array($results)) {
        include __DIR__ . '/vat_results.php';
    } else {
        echo $results; // This will display a messages
        echo '<br><br>';
        echo $form;
    }
   
} else {
    echo $form;
}
?>
<script>
// Function to validate manual VAT
function validateVat(save) {
    const input = document.getElementById('manualVat').value.trim();
    const resultDiv = document.getElementById('manualVatResult');

    // Simple validation: must start with IT and have 11 digits
    const regex = /^IT\d{11}$/;

    if (regex.test(input)) {
        
        resultDiv.style.color = 'green';            
        if(!save){
            resultDiv.textContent = "Valid VAT.";
            return;
        }

        // Send to backend
        fetch('index.php?route=manual-vat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ vat_number: input })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success){
                resultDiv.textContent = "VAT successfully inserted into the database.";
            } else {
                resultDiv.style.color = 'red';
                resultDiv.textContent = "Error: " + data.message;
            }
        })
        .catch(err => {
            resultDiv.style.color = 'red';
            resultDiv.textContent = "Error connecting to the server.";
        });

    } else {
        resultDiv.style.color = 'red';
        resultDiv.textContent = "Invalid VAT. It must start with IT and have 11 digits.";
    }
}
</script>
</body>
</html>