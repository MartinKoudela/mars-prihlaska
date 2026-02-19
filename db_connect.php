<?php

$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $envVars = parse_ini_file($envFile);
    if ($envVars) {
        foreach ($envVars as $key => $value) {
            $_ENV[$key] = $value;
        }
    }
}

$db_host = $_ENV['DB_HOST'] ?? 'localhost';
$db_user = $_ENV['DB_USER'] ?? 'root';
$db_password = $_ENV['DB_PASSWORD'] ?? '';
$db_name = $_ENV['DB_NAME'] ?? 'mars_prihlaska';

$conn = new mysqli($db_host, $db_user, $db_password, $db_name);
if ($conn->connect_errno) {
    http_response_code(500);
    die("Připojení k databázi selhalo: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
