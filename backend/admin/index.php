<?php
$pageTitle = 'Dashboard';
require __DIR__ . '/_layout_top.php';

$cars = db()->query('SELECT * FROM cars ORDER BY id DESC')->fetchAll();
$products = db()->query('SELECT * FROM products ORDER BY id DESC')->fetchAll();
?>

<?php if (!empty($_GET['flash'])): ?>
    <div class="flash"><?= htmlspecialchars($_GET['flash']) ?></div>
<?php endif; ?>

<h1>Cars <a class="btn" href="car_form.php" style="float:right;">+ Add Car</a></h1>
<table>
    <tr>
        <th>Name</th><th>Year</th><th>Mileage</th><th>Price</th><th>Transmission</th><th>Fuel</th><th>Status</th><th></th>
    </tr>
    <?php foreach ($cars as $car): ?>
    <tr>
        <td><?= htmlspecialchars($car['name']) ?></td>
        <td><?= htmlspecialchars($car['year']) ?></td>
        <td><?= number_format($car['mileage']) ?> mi</td>
        <td>£<?= number_format($car['price'], 2) ?></td>
        <td><?= htmlspecialchars($car['transmission']) ?></td>
        <td><?= htmlspecialchars($car['fuel_type']) ?></td>
        <td>
            <?php if ($car['status'] === 'sold'): ?>
                <span style="color:#f87171;font-weight:bold;">SOLD</span>
            <?php else: ?>
                <span style="color:#00FFCC;">Available</span>
            <?php endif; ?>
        </td>
        <td class="actions">
            <a href="car_form.php?id=<?= $car['id'] ?>">Edit</a>
            <form class="inline" method="POST" action="car_toggle_status.php">
                <input type="hidden" name="id" value="<?= $car['id'] ?>">
                <a href="#" onclick="this.closest('form').requestSubmit(); return false;">
                    <?= $car['status'] === 'sold' ? 'Mark Available' : 'Mark Sold' ?>
                </a>
            </form>
            <form class="inline" method="POST" action="car_delete.php" onsubmit="return confirm('Delete this car?');">
                <input type="hidden" name="id" value="<?= $car['id'] ?>">
                <a href="#" onclick="this.closest('form').requestSubmit(); return false;" style="color:#f87171;">Delete</a>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<h1 style="margin-top:3rem;">Car Products <a class="btn" href="product_form.php" style="float:right;">+ Add Product</a></h1>
<table>
    <tr>
        <th>Name</th><th>Category</th><th>Price</th><th></th>
    </tr>
    <?php foreach ($products as $product): ?>
    <tr>
        <td><?= htmlspecialchars($product['name']) ?></td>
        <td><?= htmlspecialchars($product['category']) ?></td>
        <td>£<?= number_format($product['price'], 2) ?></td>
        <td class="actions">
            <a href="product_form.php?id=<?= $product['id'] ?>">Edit</a>
            <form class="inline" method="POST" action="product_delete.php" onsubmit="return confirm('Delete this product?');">
                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                <a href="#" onclick="this.closest('form').requestSubmit(); return false;" style="color:#f87171;">Delete</a>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
