<?php
$pageTitle = 'Car Form';
require __DIR__ . '/_layout_top.php';

$id = $_GET['id'] ?? null;
$car = ['name' => '', 'year' => '', 'mileage' => '', 'price' => '', 'transmission' => 'Automatic', 'fuel_type' => 'Petrol', 'image' => ''];

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
    $image = trim($_POST['image']);

    if ($id) {
        $stmt = db()->prepare('UPDATE cars SET name=?, year=?, mileage=?, price=?, transmission=?, fuel_type=?, image=? WHERE id=?');
        $stmt->execute([$name, $year, $mileage, $price, $transmission, $fuelType, $image, $id]);
        $flash = 'Car updated.';
    } else {
        $stmt = db()->prepare('INSERT INTO cars (name, year, mileage, price, transmission, fuel_type, image) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $year, $mileage, $price, $transmission, $fuelType, $image]);
        $flash = 'Car added.';
    }

    header('Location: index.php?flash=' . urlencode($flash));
    exit;
}
?>

<h1><?= $id ? 'Edit Car' : 'Add Car' ?></h1>

<form method="POST" class="card">
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

    <label>Image URL</label>
    <input type="text" name="image" value="<?= htmlspecialchars($car['image']) ?>" required>

    <button type="submit" class="btn"><?= $id ? 'Save Changes' : 'Add Car' ?></button>
    <a href="index.php" class="btn btn-secondary" style="margin-left:0.5rem;">Cancel</a>
</form>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
