<?php
require __DIR__ . '/../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

$cars = db()->query("SELECT id, name, year, mileage, price, transmission, fuel_type AS fuelType, image, status FROM cars ORDER BY (status = 'sold') ASC, id DESC")->fetchAll();

foreach ($cars as &$car) {
    $car['year'] = (int) $car['year'];
    $car['mileage'] = (int) $car['mileage'];
    $car['price'] = (float) $car['price'];
}

echo json_encode($cars);
