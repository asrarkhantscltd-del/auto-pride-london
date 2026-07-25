<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../auth.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    $stmt = db()->prepare('SELECT status FROM cars WHERE id = ?');
    $stmt->execute([$_POST['id']]);
    $car = $stmt->fetch();

    if ($car) {
        $newStatus = $car['status'] === 'sold' ? 'available' : 'sold';
        $stmt = db()->prepare('UPDATE cars SET status = ? WHERE id = ?');
        $stmt->execute([$newStatus, $_POST['id']]);
    }
}

header('Location: index.php');
exit;
