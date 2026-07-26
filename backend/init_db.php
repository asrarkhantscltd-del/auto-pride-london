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
    $pdo->exec('CREATE TABLE IF NOT EXISTS car_images (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        car_id INTEGER NOT NULL,
        image TEXT NOT NULL,
        sort_order INTEGER NOT NULL DEFAULT 0,
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
    $pdo->exec('CREATE TABLE IF NOT EXISTS site_settings (
        setting_key TEXT NOT NULL PRIMARY KEY,
        setting_value TEXT,
        updated_at TEXT DEFAULT CURRENT_TIMESTAMP
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS social_links (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        platform TEXT NOT NULL,
        url TEXT NOT NULL,
        display_order INTEGER NOT NULL DEFAULT 0,
        is_active INTEGER NOT NULL DEFAULT 1,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS testimonials (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        customer_name TEXT NOT NULL,
        review_text TEXT NOT NULL,
        rating INTEGER NOT NULL DEFAULT 5,
        photo TEXT DEFAULT NULL,
        is_active INTEGER NOT NULL DEFAULT 1,
        sort_order INTEGER NOT NULL DEFAULT 0,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS chatbot_faqs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        question TEXT NOT NULL,
        answer TEXT NOT NULL,
        keywords TEXT DEFAULT NULL,
        category TEXT DEFAULT \'General\',
        sort_order INTEGER NOT NULL DEFAULT 0,
        is_active INTEGER NOT NULL DEFAULT 1,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS feature_toggles (
        feature_key TEXT NOT NULL PRIMARY KEY,
        is_enabled INTEGER NOT NULL DEFAULT 1,
        updated_at TEXT DEFAULT CURRENT_TIMESTAMP
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

// Seed site_settings defaults (skip if any exist)
$settingsCount = (int) $pdo->query('SELECT COUNT(*) AS c FROM site_settings')->fetch()['c'];
if ($settingsCount === 0) {
    $defaults = [
        ['business_name', 'Auto Pride London'],
        ['tagline', 'Premium Refurbished Cars & Accessories'],
        ['phone', '+44 7771 406382'],
        ['email', 'sales@autoprideldn.co.uk'],
        ['address', 'Sunbury-on-Thames'],
        ['hero_title', 'Auto Pride London'],
        ['hero_subtitle', 'Find your perfect refurbished car or upgrade with our premium accessories'],
        ['hero_background_image', ''],
        ['hero_background_video', ''],
        ['stats_cars_sold', '150'],
        ['stats_happy_customers', '120'],
        ['stats_years_in_business', '5'],
        ['whatsapp_phone', '447771406382'],
        ['whatsapp_message', 'Hi, I am interested in a car from Auto Pride London'],
        ['whatsapp_enabled', '1'],
        ['chatbot_enabled', '1'],
        ['chatbot_greeting', 'Hi! How can I help you today? Ask me about our cars, services, or location.'],
    ];
    $stmt = $pdo->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)');
    foreach ($defaults as $d) {
        $stmt->execute($d);
    }
}

// Seed feature toggles (skip if any exist)
$toggleCount = (int) $pdo->query('SELECT COUNT(*) AS c FROM feature_toggles')->fetch()['c'];
if ($toggleCount === 0) {
    $toggles = [
        ['hero_section', 1],
        ['stats_section', 1],
        ['testimonials_section', 1],
        ['whatsapp_button', 1],
        ['chatbot_widget', 1],
        ['products_page', 1],
        ['inquiry_form', 1],
    ];
    $stmt = $pdo->prepare('INSERT INTO feature_toggles (feature_key, is_enabled) VALUES (?, ?)');
    foreach ($toggles as $t) {
        $stmt->execute($t);
    }
}

// Seed chatbot FAQs (skip if any exist)
$faqCount = (int) $pdo->query('SELECT COUNT(*) AS c FROM chatbot_faqs')->fetch()['c'];
if ($faqCount === 0) {
    $faqs = [
        ['What cars do you have available?', 'We have a range of quality refurbished cars. Visit our Cars page to see the full inventory, or contact us for specific requirements.', 'cars,available,stock,inventory,vehicles,what,have', 'Cars', 1],
        ['Where are you located?', 'We are based in Sunbury-on-Thames, easily accessible from London and surrounding areas.', 'where,location,located,address,directions,find,visit', 'Location', 2],
        ['Do you offer finance?', 'Please contact us directly to discuss financing options for your chosen vehicle.', 'finance,financing,payment,monthly,installment,loan,credit', 'Finance', 3],
        ['How can I contact you?', 'Call us at +44 7771 406382, email sales@autoprideldn.co.uk, or use the enquiry form on our website. We also respond on WhatsApp!', 'contact,phone,call,email,reach,touch,whatsapp', 'Contact', 4],
        ['Do you deliver cars?', 'Delivery options may be available depending on your location. Please get in touch to discuss.', 'deliver,delivery,transport,ship,shipping,bring,collect', 'Delivery', 5],
    ];
    $stmt = $pdo->prepare('INSERT INTO chatbot_faqs (question, answer, keywords, category, sort_order) VALUES (?, ?, ?, ?, ?)');
    foreach ($faqs as $faq) {
        $stmt->execute($faq);
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
