<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require __DIR__ . '/../config.php';

$testimonials = db()->query('SELECT id, customer_name, review_text, rating, photo FROM testimonials WHERE is_active = 1 ORDER BY sort_order ASC, id DESC')->fetchAll();

echo json_encode($testimonials);
