<?php
require __DIR__ . '/../vendor/autoload.php';

use Src\Infrastructure\Persistence\MySQLConnection;
use Src\Infrastructure\Persistence\VatRepository;
use Src\Domain\Service\VatValidator;
use Src\Application\Service\ProcessVatFileService;
use Src\Infrastructure\FileUpload\CsvVatReader;
use Src\Presentation\Controller\VatController;
use Src\Application\Service\ProcessSingleVatService;


$routes = require __DIR__ . '/../config/routes.php';

$connection = MySQLConnection::getConnection();

$route = $_GET['route'] ?? '/';

if (!isset($routes[$route])) {
    http_response_code(404);
    echo "404 - Route not found";
    exit;
}

// Get class and method
list($controllerClass, $method) = $routes[$route];

// Instantiate controller and call method
$repository = new VatRepository($connection);
$validator = new VatValidator();
$service = new ProcessVatFileService($repository, $validator);
$csvReader = new CsvVatReader();
$singleservice = new ProcessSingleVatService($repository, $validator);

$controller = new VatController($service, $csvReader, $singleservice);
$controller->$method();
