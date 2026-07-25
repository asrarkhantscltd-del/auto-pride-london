<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../auth.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    $stmt = db()->prepare('DELETE FROM cars WHERE id = ?');
    $stmt->execute([$_POST['id']]);
}

header('Location: index.php?flash=' . urlencode('Car deleted.'));
exit;
