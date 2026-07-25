<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../auth.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT * FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        header('Location: index.php');
        exit;
    }

    $error = 'Invalid username or password.';
}

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login — Auto Pride London</title>
    <style>
        body { font-family: sans-serif; background: #0F0F12; color: #fff; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        form { background: #18181C; padding: 2rem; border-radius: 16px; width: 300px; }
        h1 { font-size: 1.25rem; margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.25rem; font-size: 0.875rem; color: #999; }
        input { width: 100%; padding: 0.6rem; margin-bottom: 1rem; border-radius: 8px; border: 1px solid #333; background: #0F0F12; color: #fff; box-sizing: border-box; }
        button { width: 100%; padding: 0.7rem; border-radius: 8px; border: none; background: #00FFCC; color: #000; font-weight: bold; cursor: pointer; }
        .error { color: #f87171; margin-bottom: 1rem; font-size: 0.875rem; }
    </style>
</head>
<body>
    <form method="POST">
        <h1>Auto Pride London — Admin</h1>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <label>Username</label>
        <input type="text" name="username" required autofocus>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit">Log In</button>
    </form>
</body>
</html>
