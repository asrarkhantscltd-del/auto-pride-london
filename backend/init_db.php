<?php

// One-time setup script. Run once (via browser or `php init_db.php`) to
// create tables and seed initial data. Safe to re-run — it won't duplicate
// data if rows already exist.

require __DIR__ . '/config.php';

$pdo = db();
$driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

if ($driver === 'sqlite') {
    $pdo->exec('CREATE TABLE IF NOT EXISTS admins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password_hash TEXT NOT NULL,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS cars (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        year INTEGER NOT NULL,
        mileage INTEGER NOT NULL,
        price REAL NOT NULL,
        transmission TEXT NOT NULL,
        fuel_type TEXT NOT NULL,
        image TEXT NOT NULL,
        status TEXT NOT NULL DEFAULT \'available\',
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        category TEXT NOT NULL,
        price REAL NOT NULL,
        image TEXT NOT NULL,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS enquiries (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        car_interest TEXT,
        message TEXT NOT NULL,
        is_read INTEGER NOT NULL DEFAULT 0,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )');
} else {
    $sql = file_get_contents(__DIR__ . '/schema.mysql.sql');
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $statement) {
        if ($statement !== '' && stripos($statement, '--') !== 0) {
            $pdo->exec($statement);
        }
    }
}

// Seed an admin user if none exists yet.
$count = (int) $pdo->query('SELECT COUNT(*) AS c FROM admins')->fetch()['c'];
$generatedPassword = null;

if ($count === 0) {
    $generatedPassword = bin2hex(random_bytes(6)); // 12-char random password
    $hash = password_hash($generatedPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?)');
    $stmt->execute(['admin', $hash]);
}

// Seed cars if the table is empty.
$carCount = (int) $pdo->query('SELECT COUNT(*) AS c FROM cars')->fetch()['c'];
if ($carCount === 0) {
    $cars = [
        ['BMW 3 Series', 2021, 18500, 22995, 'Automatic', 'Petrol', 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800'],
        ['Audi A4', 2020, 24300, 20495, 'Automatic', 'Diesel', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800'],
        ['Mercedes C-Class', 2022, 9800, 27995, 'Automatic', 'Hybrid', 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800'],
        ['Volkswagen Golf', 2019, 32100, 14495, 'Manual', 'Petrol', 'https://images.unsplash.com/photo-1494905998402-395d579af36f?w=800'],
        ['Tesla Model 3', 2022, 15200, 31995, 'Automatic', 'Electric', 'https://images.unsplash.com/photo-1542362567-b07e54358753?w=800'],
        ['Ford Focus', 2018, 41200, 11495, 'Manual', 'Petrol', 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800'],
        ['Range Rover Evoque', 2021, 21300, 33995, 'Automatic', 'Diesel', 'https://images.unsplash.com/photo-1617531653332-bd46c24f2068?w=800'],
        ['Toyota Corolla', 2020, 27800, 16495, 'Automatic', 'Hybrid', 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=800'],
    ];
    $stmt = $pdo->prepare('INSERT INTO cars (name, year, mileage, price, transmission, fuel_type, image) VALUES (?, ?, ?, ?, ?, ?, ?)');
    foreach ($cars as $car) {
        $stmt->execute($car);
    }
}

// Seed products if the table is empty.
$productCount = (int) $pdo->query('SELECT COUNT(*) AS c FROM products')->fetch()['c'];
if ($productCount === 0) {
    $products = [
        ['All-Weather Floor Mats', 'Car Mats', 34.99, 'https://images.unsplash.com/photo-1621007806737-b90c29c3d3ad?w=600'],
        ['Premium Luxury Car Perfume', 'Car Perfume', 12.99, 'https://images.unsplash.com/photo-1595124790696-2f676dbe1f4e?w=600'],
        ['Leather Seat Covers (Set)', 'Car Seats', 89.99, 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=600'],
        ['Blind Spot Mirror Set', 'Car Mirrors', 9.99, 'https://images.unsplash.com/photo-1600661653561-629509216228?w=600'],
        ['Carbon Fibre Steering Wheel Cover', 'Steering Covers', 19.99, 'https://images.unsplash.com/photo-1605559911160-a3d95d213904?w=600'],
        ['Racing Stripe Decal Set', 'Car Stickers', 24.99, 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=600'],
        ['Heavy-Duty Rubber Mats', 'Car Mats', 29.99, 'https://images.unsplash.com/photo-1610647752706-3bb12232b3ab?w=600'],
        ['New Car Scent Diffuser', 'Car Perfume', 8.99, 'https://images.unsplash.com/photo-1585386959984-a4155224a1ad?w=600'],
    ];
    $stmt = $pdo->prepare('INSERT INTO products (name, category, price, image) VALUES (?, ?, ?, ?)');
    foreach ($products as $product) {
        $stmt->execute($product);
    }
}

header('Content-Type: text/plain');
echo "Database setup complete.\n";
echo "Cars: " . $pdo->query('SELECT COUNT(*) AS c FROM cars')->fetch()['c'] . "\n";
echo "Products: " . $pdo->query('SELECT COUNT(*) AS c FROM products')->fetch()['c'] . "\n";

if ($generatedPassword !== null) {
    echo "\nAdmin account created:\n";
    echo "  Username: admin\n";
    echo "  Password: {$generatedPassword}\n";
    echo "IMPORTANT: Save this password now — it will not be shown again. Change it after logging in.\n";
} else {
    echo "\nAdmin account already exists — no new password generated.\n";
}
