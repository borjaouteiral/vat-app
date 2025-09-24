<?php
namespace Src\Infrastructure\Persistence;

use PDO;

class MySQLConnection
{
    public static function getConnection(): PDO
    {
        return new PDO('mysql:host=localhost;dbname=vat_app', 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }
}
?>