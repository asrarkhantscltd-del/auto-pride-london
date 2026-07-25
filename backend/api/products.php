<?php
require __DIR__ . '/../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

$products = db()->query('SELECT id, name, category, price, image FROM products ORDER BY id DESC')->fetchAll();

foreach ($products as &$product) {
    $product['price'] = (float) $product['price'];
}

echo json_encode($products);
