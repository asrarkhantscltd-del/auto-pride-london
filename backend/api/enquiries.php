<?php
require __DIR__ . '/../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];

$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$carInterest = trim($input['carInterest'] ?? '');
$message = trim($input['message'] ?? '');
$honeypot = trim($input['website'] ?? '');
$renderedAt = (int) ($input['renderedAt'] ?? 0);

// Bots either fill the hidden honeypot field or submit faster than a human
// could read and complete the form. Pretend success so they don't learn
// to adapt, but skip the database write.
$submittedTooFast = $renderedAt > 0 && (microtime(true) * 1000 - $renderedAt) < 2000;
if ($honeypot !== '' || $submittedTooFast) {
    echo json_encode(['success' => true]);
    exit;
}

if ($name === '' || $email === '' || $message === '') {
    http_response_code(422);
    echo json_encode(['error' => 'Name, email, and message are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['error' => 'Please provide a valid email address.']);
    exit;
}

$driver = db()->getAttribute(PDO::ATTR_DRIVER_NAME);
$cutoffSql = $driver === 'mysql'
    ? 'NOW() - INTERVAL 10 MINUTE'
    : "datetime('now', '-10 minutes')";
$stmt = db()->prepare("SELECT COUNT(*) AS c FROM enquiries WHERE email = ? AND created_at > ({$cutoffSql})");
$stmt->execute([$email]);
$recentCount = (int) $stmt->fetch()['c'];

if ($recentCount >= 3) {
    http_response_code(429);
    echo json_encode(['error' => 'Too many enquiries from this email recently. Please try again later.']);
    exit;
}

$stmt = db()->prepare('INSERT INTO enquiries (name, email, car_interest, message) VALUES (?, ?, ?, ?)');
$stmt->execute([$name, $email, $carInterest ?: null, $message]);

echo json_encode(['success' => true]);
