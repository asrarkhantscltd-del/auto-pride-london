<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../auth.php';
requireAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — Auto Pride London</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: sans-serif; background: #0F0F12; color: #fff; margin: 0; }
        header { background: #18181C; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #2a2a2e; }
        header a { color: #fff; text-decoration: none; margin-right: 1.5rem; font-size: 0.9rem; }
        header a.brand { font-weight: bold; color: #00FFCC; margin-right: 2rem; }
        header a.logout { color: #f87171; margin-right: 0; }
        main { padding: 2rem; max-width: 1000px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; }
        th, td { text-align: left; padding: 0.75rem; border-bottom: 1px solid #2a2a2e; font-size: 0.9rem; }
        th { color: #999; font-weight: 500; }
        .btn { display: inline-block; padding: 0.5rem 1rem; border-radius: 8px; background: #00FFCC; color: #000; font-weight: bold; text-decoration: none; font-size: 0.85rem; }
        .btn-secondary { background: #2a2a2e; color: #fff; }
        .btn-danger { background: #f87171; color: #000; }
        .actions a { margin-right: 0.5rem; font-size: 0.85rem; }
        form.inline { display: inline; }
        label { display: block; margin-bottom: 0.25rem; font-size: 0.875rem; color: #999; }
        input, select { width: 100%; padding: 0.6rem; margin-bottom: 1rem; border-radius: 8px; border: 1px solid #333; background: #18181C; color: #fff; }
        .card { background: #18181C; padding: 1.5rem; border-radius: 16px; max-width: 500px; }
        .flash { background: #00FFCC22; border: 1px solid #00FFCC55; color: #00FFCC; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; }
    </style>
</head>
<body>
    <?php $unreadCount = (int) db()->query('SELECT COUNT(*) AS c FROM enquiries WHERE is_read = 0')->fetch()['c']; ?>
    <header>
        <div>
            <a class="brand" href="index.php">Auto Pride London</a>
            <a href="index.php">Dashboard</a>
            <a href="car_form.php">Add Car</a>
            <a href="product_form.php">Add Product</a>
            <a href="enquiries.php">Enquiries<?= $unreadCount > 0 ? ' <span style="background:#00FFCC;color:#000;border-radius:10px;padding:1px 7px;font-size:0.75rem;font-weight:bold;">' . $unreadCount . '</span>' : '' ?></a>
        </div>
        <div>
            <a href="settings.php">Account Settings</a>
            <a class="logout" href="logout.php">Log Out</a>
        </div>
    </header>
    <main>
