<?php
$pageTitle = 'Product Form';
require __DIR__ . '/_layout_top.php';

$id = $_GET['id'] ?? null;
$product = ['name' => '', 'category' => 'Car Mats', 'price' => '', 'image' => ''];

if ($id) {
    $stmt = db()->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if ($found) {
        $product = $found;
    }
}

$categories = ['Car Mats', 'Car Perfume', 'Car Seats', 'Car Mirrors', 'Steering Covers', 'Car Stickers'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $price = (float) $_POST['price'];
    $image = trim($_POST['image']);

    if ($id) {
        $stmt = db()->prepare('UPDATE products SET name=?, category=?, price=?, image=? WHERE id=?');
        $stmt->execute([$name, $category, $price, $image, $id]);
        $flash = 'Product updated.';
    } else {
        $stmt = db()->prepare('INSERT INTO products (name, category, price, image) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $category, $price, $image]);
        $flash = 'Product added.';
    }

    header('Location: index.php?flash=' . urlencode($flash));
    exit;
}
?>

<h1><?= $id ? 'Edit Product' : 'Add Product' ?></h1>

<form method="POST" class="card">
    <label>Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

    <label>Category</label>
    <select name="category">
        <?php foreach ($categories as $opt): ?>
            <option value="<?= $opt ?>" <?= $product['category'] === $opt ? 'selected' : '' ?>><?= $opt ?></option>
        <?php endforeach; ?>
    </select>

    <label>Price (£)</label>
    <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>

    <label>Image URL</label>
    <input type="text" name="image" value="<?= htmlspecialchars($product['image']) ?>" required>

    <button type="submit" class="btn"><?= $id ? 'Save Changes' : 'Add Product' ?></button>
    <a href="index.php" class="btn btn-secondary" style="margin-left:0.5rem;">Cancel</a>
</form>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
