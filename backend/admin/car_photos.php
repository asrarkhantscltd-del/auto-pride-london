<?php
$pageTitle = 'Manage Photos';
require __DIR__ . '/_layout_top.php';
require __DIR__ . '/../upload.php';

$carId = $_GET['car_id'] ?? null;
$uploadError = null;

if (!$carId) {
    header('Location: index.php');
    exit;
}

$stmt = db()->prepare('SELECT * FROM cars WHERE id = ?');
$stmt->execute([$carId]);
$car = $stmt->fetch();

if (!$car) {
    header('Location: index.php');
    exit;
}

const MAX_IMAGES_PER_CAR = 20;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete_image_id'])) {
    $stmt = db()->prepare('SELECT image FROM car_images WHERE id = ? AND car_id = ?');
    $stmt->execute([$_POST['delete_image_id'], $carId]);
    $image = $stmt->fetch();

    if ($image) {
        $filePath = __DIR__ . '/../..' . $image['image'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $stmt = db()->prepare('DELETE FROM car_images WHERE id = ?');
        $stmt->execute([$_POST['delete_image_id']]);
    }

    header('Location: car_photos.php?car_id=' . $carId);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['images'])) {
    $countStmt = db()->prepare('SELECT COUNT(*) AS c FROM car_images WHERE car_id = ?');
    $countStmt->execute([$carId]);
    $existingCount = (int) $countStmt->fetch()['c'];

    $uploaded = handleMultipleImageUploads('images');

    $available = MAX_IMAGES_PER_CAR - $existingCount;
    if (count($uploaded) > $available) {
        $uploadError = 'Only ' . $available . ' more photo(s) could be added (max ' . MAX_IMAGES_PER_CAR . ' per car). The rest were not saved.';
        $uploaded = array_slice($uploaded, 0, max(0, $available));
    }

    if (!empty($uploaded)) {
        $stmt = db()->prepare('INSERT INTO car_images (car_id, image, sort_order) VALUES (?, ?, ?)');
        foreach ($uploaded as $i => $path) {
            $stmt->execute([$carId, $path, $existingCount + $i]);
        }
    }

    if (!$uploadError) {
        header('Location: car_photos.php?car_id=' . $carId . '&flash=' . urlencode('Photos added.'));
        exit;
    }
}

$images = db()->prepare('SELECT * FROM car_images WHERE car_id = ? ORDER BY sort_order ASC, id ASC');
$images->execute([$carId]);
$images = $images->fetchAll();
$imageCount = count($images);
?>

<h1>Manage Photos — <?= htmlspecialchars($car['name']) ?></h1>

<?php if (!empty($_GET['flash'])): ?>
    <div class="flash"><?= htmlspecialchars($_GET['flash']) ?></div>
<?php endif; ?>

<?php if ($uploadError): ?>
    <div class="flash" style="background:#f8717122;border-color:#f8717155;color:#f87171;"><?= htmlspecialchars($uploadError) ?></div>
<?php endif; ?>

<p style="color:#999;margin-bottom:1.5rem;"><?= $imageCount ?> / <?= MAX_IMAGES_PER_CAR ?> photos</p>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:1rem;margin-bottom:2rem;">
    <?php foreach ($images as $image): ?>
        <div style="position:relative;">
            <img src="<?= htmlspecialchars($image['image']) ?>" alt="" style="width:100%;height:100px;object-fit:cover;border-radius:8px;">
            <form method="POST" style="position:absolute;top:4px;right:4px;">
                <input type="hidden" name="delete_image_id" value="<?= $image['id'] ?>">
                <button type="submit" onclick="return confirm('Delete this photo?');" style="background:#f87171;color:#000;border:none;border-radius:50%;width:24px;height:24px;cursor:pointer;font-weight:bold;line-height:1;">×</button>
            </form>
        </div>
    <?php endforeach; ?>
    <?php if ($imageCount === 0): ?>
        <p style="color:#666;">No additional photos yet.</p>
    <?php endif; ?>
</div>

<?php if ($imageCount < MAX_IMAGES_PER_CAR): ?>
<form method="POST" enctype="multipart/form-data" class="card">
    <label>Add Photos (up to <?= MAX_IMAGES_PER_CAR - $imageCount ?> more, inside/outside shots)</label>
    <input type="file" name="images[]" accept="image/png,image/jpeg,image/webp,image/gif" multiple required>
    <button type="submit" class="btn">Upload Photos</button>
</form>
<?php else: ?>
    <p style="color:#666;">Maximum of <?= MAX_IMAGES_PER_CAR ?> photos reached for this car.</p>
<?php endif; ?>

<p style="margin-top:1.5rem;"><a href="index.php" style="color:#999;">← Back to Dashboard</a></p>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
