<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../auth.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    $imagesStmt = db()->prepare('SELECT image FROM car_images WHERE car_id = ?');
    $imagesStmt->execute([$_POST['id']]);
    foreach ($imagesStmt->fetchAll() as $row) {
        $filePath = __DIR__ . '/../..' . $row['image'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $stmt = db()->prepare('DELETE FROM cars WHERE id = ?');
    $stmt->execute([$_POST['id']]);
}

header('Location: index.php?flash=' . urlencode('Car deleted.'));
exit;
