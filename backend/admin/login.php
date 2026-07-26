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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Auto Pride London</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-card { width: 340px; }
        .login-logo { text-align: center; margin-bottom: 1.5rem; }
        .login-logo svg { width: 40px; height: 40px; stroke: var(--accent); fill: none; stroke-width: 2; }
        .login-logo h1 { font-size: 1.15rem; margin-top: 0.5rem; }
        .login-logo span { color: var(--accent); }
    </style>
</head>
<body>
    <form method="POST" class="card login-card">
        <div class="login-logo">
            <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 22 4 22 16 16 12 16 8"/></svg>
            <h1>Auto Pride <span>London</span></h1>
        </div>

        <?php if ($error): ?>
            <div class="flash flash-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required autofocus>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Log In</button>
    </form>
</body>
</html>
