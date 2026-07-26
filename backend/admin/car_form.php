<?php
$pageTitle = 'Car Form';
require __DIR__ . '/_layout_top.php';
require __DIR__ . '/../upload.php';

$uploadError = null;

$id = $_GET['id'] ?? null;
$car = ['name' => '', 'year' => '', 'mileage' => '', 'price' => '', 'transmission' => 'Automatic', 'fuel_type' => 'Petrol', 'image' => '', 'status' => 'available'];

if ($id) {
    $stmt = db()->prepare('SELECT * FROM cars WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if ($found) {
        $car = $found;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $year = (int) $_POST['year'];
    $mileage = (int) $_POST['mileage'];
    $price = (float) $_POST['price'];
    $transmission = trim($_POST['transmission']);
    $fuelType = trim($_POST['fuel_type']);
    $status = $_POST['status'] === 'sold' ? 'sold' : 'available';

    try {
        $uploadedImage = handleImageUpload('image');
        $image = $uploadedImage ?? $car['image'];

        if (!$id && $uploadedImage === null) {
            throw new RuntimeException('Please choose an image to upload.');
        }

        if ($id) {
            $stmt = db()->prepare('UPDATE cars SET name=?, year=?, mileage=?, price=?, transmission=?, fuel_type=?, image=?, status=? WHERE id=?');
            $stmt->execute([$name, $year, $mileage, $price, $transmission, $fuelType, $image, $status, $id]);
            $flash = 'Car updated.';
        } else {
            $stmt = db()->prepare('INSERT INTO cars (name, year, mileage, price, transmission, fuel_type, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$name, $year, $mileage, $price, $transmission, $fuelType, $image, $status]);
            $flash = 'Car added.';
        }

        header('Location: index.php?flash=' . urlencode($flash));
        exit;
    } catch (RuntimeException $e) {
        $uploadError = $e->getMessage();
        $car = array_merge($car, ['name' => $name, 'year' => $year, 'mileage' => $mileage, 'price' => $price, 'transmission' => $transmission, 'fuel_type' => $fuelType, 'status' => $status]);
    }
}
?>

<h1><?= $id ? 'Edit Car' : 'Add Car' ?></h1>

<?php if ($id): ?>
    <p><a href="car_photos.php?car_id=<?= $id ?>" class="btn btn-secondary" style="display:inline-block;margin-bottom:1.5rem;">Manage Photos (inside/outside gallery)</a></p>
<?php endif; ?>

<?php if ($uploadError): ?>
    <div class="flash" style="background:#f8717122;border-color:#f8717155;color:#f87171;"><?= htmlspecialchars($uploadError) ?></div>
<?php endif; ?>

<form method="POST" class="card" enctype="multipart/form-data">
    <label>Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($car['name']) ?>" required>

    <label>Year</label>
    <input type="number" name="year" value="<?= htmlspecialchars($car['year']) ?>" required>

    <label>Mileage (mi)</label>
    <input type="number" name="mileage" value="<?= htmlspecialchars($car['mileage']) ?>" required>

    <label>Price (£)</label>
    <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($car['price']) ?>" required>

    <label>Transmission</label>
    <select name="transmission">
        <?php foreach (['Automatic', 'Manual'] as $opt): ?>
            <option value="<?= $opt ?>" <?= $car['transmission'] === $opt ? 'selected' : '' ?>><?= $opt ?></option>
        <?php endforeach; ?>
    </select>

    <label>Fuel Type</label>
    <select name="fuel_type">
        <?php foreach (['Petrol', 'Diesel', 'Hybrid', 'Electric'] as $opt): ?>
            <option value="<?= $opt ?>" <?= $car['fuel_type'] === $opt ? 'selected' : '' ?>><?= $opt ?></option>
        <?php endforeach; ?>
    </select>

    <label>Image</label>
    <?php if (!empty($car['image'])): ?>
        <img src="<?= htmlspecialchars($car['image']) ?>" alt="Current image" style="max-width:100%;max-height:150px;border-radius:8px;margin-bottom:0.5rem;display:block;">
    <?php endif; ?>
    <input type="file" name="image" accept="image/png,image/jpeg,image/webp,image/gif" <?= $id ? '' : 'required' ?>>
    <?php if ($id): ?>
        <small style="color:#666;display:block;margin-top:-0.75rem;margin-bottom:1rem;">Leave empty to keep the current image.</small>
    <?php endif; ?>

    <label>Status</label>
    <select name="status">
        <?php foreach (['available' => 'Available', 'sold' => 'Sold'] as $val => $label): ?>
            <option value="<?= $val ?>" <?= $car['status'] === $val ? 'selected' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn"><?= $id ? 'Save Changes' : 'Add Car' ?></button>
    <a href="index.php" class="btn btn-secondary" style="margin-left:0.5rem;">Cancel</a>
</form>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
