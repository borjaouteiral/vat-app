<?php
echo "Running database setup...\n";

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$sqlFile = __DIR__ . '/vat_app.sql';

$mysqli = new mysqli($host, $user, $pass);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$sql = file_get_contents($sqlFile);

if ($mysqli->multi_query($sql)) {
    echo "Database and tables created successfully.\n";
} else {
    echo "Error importing database: " . $mysqli->error . "\n";
}

$mysqli->close();
