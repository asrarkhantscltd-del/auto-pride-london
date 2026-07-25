<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/plain');

echo "PHP version: " . phpversion() . "\n";
echo "PDO drivers available: " . implode(', ', PDO::getAvailableDrivers()) . "\n\n";

echo "Files in backend/:\n";
foreach (scandir(__DIR__) as $f) {
    if ($f !== '.' && $f !== '..') {
        echo "  - {$f}\n";
    }
}
echo "\n";

try {
    require __DIR__ . '/config.php';
    $pdo = db();
    echo "Database connection: SUCCESS\n";
    echo "Driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n";

    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables found: " . implode(', ', $tables) . "\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " line " . $e->getLine() . "\n";
}
