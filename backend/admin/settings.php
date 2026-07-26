<?php
$pageTitle = 'Account Settings';
require __DIR__ . '/_layout_top.php';

$stmt = db()->prepare('SELECT * FROM admins WHERE id = ?');
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newUsername = trim($_POST['username'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!password_verify($currentPassword, $admin['password_hash'])) {
        $error = 'Current password is incorrect.';
    } elseif ($newUsername === '') {
        $error = 'Username cannot be empty.';
    } elseif ($newPassword !== '' && strlen($newPassword) < 8) {
        $error = 'New password must be at least 8 characters.';
    } elseif ($newPassword !== '' && $newPassword !== $confirmPassword) {
        $error = 'New password and confirmation do not match.';
    } else {
        if ($newUsername !== $admin['username']) {
            $checkStmt = db()->prepare('SELECT id FROM admins WHERE username = ? AND id != ?');
            $checkStmt->execute([$newUsername, $admin['id']]);
            if ($checkStmt->fetch()) {
                $error = 'That username is already taken.';
            }
        }

        if (!$error) {
            if ($newPassword !== '') {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = db()->prepare('UPDATE admins SET username = ?, password_hash = ? WHERE id = ?');
                $stmt->execute([$newUsername, $hash, $admin['id']]);
            } else {
                $stmt = db()->prepare('UPDATE admins SET username = ? WHERE id = ?');
                $stmt->execute([$newUsername, $admin['id']]);
            }

            $admin['username'] = $newUsername;
            $success = 'Settings updated successfully.';
        }
    }
}
?>

<h1>Account Settings</h1>

<?php if ($error): ?>
    <div class="flash" style="background:#f8717122;border-color:#f8717155;color:#f87171;"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="flash"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="POST" class="card">
    <label>Username</label>
    <input type="text" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required>

    <label>New Password (leave blank to keep current password)</label>
    <input type="password" name="new_password" placeholder="At least 8 characters" autocomplete="new-password">

    <label>Confirm New Password</label>
    <input type="password" name="confirm_password" placeholder="Repeat new password" autocomplete="new-password">

    <hr style="border:none;border-top:1px solid #2a2a2e;margin:1.5rem 0;">

    <label>Current Password (required to save any change)</label>
    <input type="password" name="current_password" required autocomplete="current-password">

    <button type="submit" class="btn">Save Changes</button>
</form>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
