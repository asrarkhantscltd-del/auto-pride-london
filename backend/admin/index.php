<?php
$pageTitle = 'Dashboard';
require __DIR__ . '/_layout_top.php';

$totalCars = (int) db()->query('SELECT COUNT(*) AS c FROM cars')->fetch()['c'];
$availableCars = (int) db()->query("SELECT COUNT(*) AS c FROM cars WHERE status = 'available'")->fetch()['c'];
$soldCars = $totalCars - $availableCars;
$totalProducts = (int) db()->query('SELECT COUNT(*) AS c FROM products')->fetch()['c'];
$unreadEnquiries = (int) db()->query('SELECT COUNT(*) AS c FROM enquiries WHERE is_read = 0')->fetch()['c'];

$cars = db()->query('SELECT * FROM cars ORDER BY id DESC')->fetchAll();
$products = db()->query('SELECT * FROM products ORDER BY id DESC')->fetchAll();
?>

<?php if (!empty($_GET['flash'])): ?>
    <div class="flash flash-success"><?= htmlspecialchars($_GET['flash']) ?></div>
<?php endif; ?>

<!-- Stat Cards -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon accent">
            <svg viewBox="0 0 24 24"><path d="M5 17h2m10 0h2M3 9l2-5h14l2 5"/><path d="M3 9h18v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-number"><?= $totalCars ?></div>
            <div class="stat-label">Total Cars</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success">
            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-number"><?= $availableCars ?></div>
            <div class="stat-label">Available</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon danger">
            <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-number"><?= $soldCars ?></div>
            <div class="stat-label">Sold</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon warning">
            <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-number"><?= $totalProducts ?></div>
            <div class="stat-label">Products</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon <?= $unreadEnquiries > 0 ? 'accent' : 'success' ?>">
            <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-number"><?= $unreadEnquiries ?></div>
            <div class="stat-label">Unread Enquiries</div>
        </div>
    </div>
</div>

<!-- Cars Table -->
<div class="table-wrap">
    <div class="table-header">
        <h2>Cars</h2>
        <a class="btn btn-primary btn-sm" href="car_form.php">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Car
        </a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Name</th><th>Year</th><th>Mileage</th><th>Price</th><th>Transmission</th><th>Fuel</th><th>Status</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($cars)): ?>
            <tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:2rem;">No cars yet. Add your first car!</td></tr>
        <?php endif; ?>
        <?php foreach ($cars as $car): ?>
            <tr>
                <td style="font-weight:500;"><?= htmlspecialchars($car['name']) ?></td>
                <td><?= htmlspecialchars($car['year']) ?></td>
                <td><?= number_format($car['mileage']) ?> mi</td>
                <td>£<?= number_format($car['price'], 2) ?></td>
                <td><?= htmlspecialchars($car['transmission']) ?></td>
                <td><?= htmlspecialchars($car['fuel_type']) ?></td>
                <td>
                    <?php if ($car['status'] === 'sold'): ?>
                        <span class="badge badge-sold">Sold</span>
                    <?php else: ?>
                        <span class="badge badge-available">Available</span>
                    <?php endif; ?>
                </td>
                <td class="actions">
                    <a href="car_form.php?id=<?= $car['id'] ?>">Edit</a>
                    <a href="car_photos.php?car_id=<?= $car['id'] ?>">Photos</a>
                    <form class="inline" method="POST" action="car_toggle_status.php">
                        <input type="hidden" name="id" value="<?= $car['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                        <a href="#" onclick="this.closest('form').requestSubmit(); return false;">
                            <?= $car['status'] === 'sold' ? 'Mark Available' : 'Mark Sold' ?>
                        </a>
                    </form>
                    <form class="inline" method="POST" action="car_delete.php" onsubmit="return confirm('Delete this car?');">
                        <input type="hidden" name="id" value="<?= $car['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                        <a href="#" onclick="this.closest('form').requestSubmit(); return false;" class="danger-link">Delete</a>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Products Table -->
<div class="table-wrap">
    <div class="table-header">
        <h2>Car Products</h2>
        <a class="btn btn-primary btn-sm" href="product_form.php">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Product
        </a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Name</th><th>Category</th><th>Price</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($products)): ?>
            <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:2rem;">No products yet. Add your first product!</td></tr>
        <?php endif; ?>
        <?php foreach ($products as $product): ?>
            <tr>
                <td style="font-weight:500;"><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['category']) ?></td>
                <td>£<?= number_format($product['price'], 2) ?></td>
                <td class="actions">
                    <a href="product_form.php?id=<?= $product['id'] ?>">Edit</a>
                    <form class="inline" method="POST" action="product_delete.php" onsubmit="return confirm('Delete this product?');">
                        <input type="hidden" name="id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                        <a href="#" onclick="this.closest('form').requestSubmit(); return false;" class="danger-link">Delete</a>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
