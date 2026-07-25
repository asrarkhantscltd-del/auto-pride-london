<?php
$pageTitle = 'Product Form';
require __DIR__ . '/_layout_top.php';
require __DIR__ . '/../upload.php';

$uploadError = null;
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

    try {
        $uploadedImage = handleImageUpload('image');
        $image = $uploadedImage ?? $product['image'];

        if (!$id && $uploadedImage === null) {
            throw new RuntimeException('Please choose an image to upload.');
        }

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
    } catch (RuntimeException $e) {
        $uploadError = $e->getMessage();
        $product = array_merge($product, ['name' => $name, 'category' => $category, 'price' => $price]);
    }
}
?>

<h1><?= $id ? 'Edit Product' : 'Add Product' ?></h1>

<?php if ($uploadError): ?>
    <div class="flash" style="background:#f8717122;border-color:#f8717155;color:#f87171;"><?= htmlspecialchars($uploadError) ?></div>
<?php endif; ?>

<form method="POST" class="card" enctype="multipart/form-data">
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

    <label>Image</label>
    <?php if (!empty($product['image'])): ?>
        <img src="<?= htmlspecialchars($product['image']) ?>" alt="Current image" style="max-width:100%;max-height:150px;border-radius:8px;margin-bottom:0.5rem;display:block;">
    <?php endif; ?>
    <input type="file" name="image" accept="image/png,image/jpeg,image/webp,image/gif" <?= $id ? '' : 'required' ?>>
    <?php if ($id): ?>
        <small style="color:#666;display:block;margin-top:-0.75rem;margin-bottom:1rem;">Leave empty to keep the current image.</small>
    <?php endif; ?>

    <button type="submit" class="btn"><?= $id ? 'Save Changes' : 'Add Product' ?></button>
    <a href="index.php" class="btn btn-secondary" style="margin-left:0.5rem;">Cancel</a>
</form>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
