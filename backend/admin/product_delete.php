<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../auth.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    $stmt = db()->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$_POST['id']]);
}

header('Location: index.php?flash=' . urlencode('Product deleted.'));
exit;
