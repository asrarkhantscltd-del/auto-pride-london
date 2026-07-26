<?php
require __DIR__ . '/../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing id']);
    exit;
}

$stmt = db()->prepare('SELECT id, name, year, mileage, price, transmission, fuel_type AS fuelType, image, status FROM cars WHERE id = ?');
$stmt->execute([$id]);
$car = $stmt->fetch();

if (!$car) {
    http_response_code(404);
    echo json_encode(['error' => 'Car not found']);
    exit;
}

$car['year'] = (int) $car['year'];
$car['mileage'] = (int) $car['mileage'];
$car['price'] = (float) $car['price'];

$imagesStmt = db()->prepare('SELECT image FROM car_images WHERE car_id = ? ORDER BY sort_order ASC, id ASC');
$imagesStmt->execute([$id]);
$galleryImages = $imagesStmt->fetchAll(PDO::FETCH_COLUMN);

$car['gallery'] = array_values(array_unique(array_merge([$car['image']], $galleryImages)));

echo json_encode($car);
