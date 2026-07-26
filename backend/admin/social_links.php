<?php
$pageTitle = 'Social Links';
require __DIR__ . '/_layout_top.php';

$success = null;
$error = null;

$platforms = [
    'instagram'  => 'Instagram',
    'facebook'   => 'Facebook',
    'tiktok'     => 'TikTok',
    'youtube'    => 'YouTube',
    'twitter'    => 'X (Twitter)',
    'whatsapp'   => 'WhatsApp',
    'snapchat'   => 'Snapchat',
    'linkedin'   => 'LinkedIn',
];

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $platform = $_POST['platform'] ?? '';
        $url = trim($_POST['url'] ?? '');
        if (empty($platform) || empty($url)) {
            $error = 'Platform and URL are required.';
        } else {
            $maxOrder = (int) db()->query('SELECT COALESCE(MAX(display_order), 0) AS m FROM social_links')->fetch()['m'];
            $stmt = db()->prepare('INSERT INTO social_links (platform, url, display_order) VALUES (?, ?, ?)');
            $stmt->execute([$platform, $url, $maxOrder + 1]);
            $success = 'Social link added.';
        }
    } elseif ($action === 'update') {
        $id = (int) ($_POST['id'] ?? 0);
        $url = trim($_POST['url'] ?? '');
        if ($id > 0 && $url !== '') {
            $stmt = db()->prepare('UPDATE social_links SET url = ?, platform = ? WHERE id = ?');
            $stmt->execute([$url, $_POST['platform'] ?? '', $id]);
            $success = 'Link updated.';
        }
    } elseif ($action === 'toggle') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = db()->prepare('UPDATE social_links SET is_active = NOT is_active WHERE id = ?');
            $stmt->execute([$id]);
            $success = 'Link toggled.';
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = db()->prepare('DELETE FROM social_links WHERE id = ?');
            $stmt->execute([$id]);
            $success = 'Link deleted.';
        }
    } elseif ($action === 'reorder') {
        $ids = $_POST['order'] ?? [];
        if (is_array($ids)) {
            $stmt = db()->prepare('UPDATE social_links SET display_order = ? WHERE id = ?');
            foreach ($ids as $i => $id) {
                $stmt->execute([$i, (int) $id]);
            }
            $success = 'Order saved.';
        }
    }
}

$links = db()->query('SELECT * FROM social_links ORDER BY display_order ASC, id ASC')->fetchAll();
?>

<div class="page-header">
    <h1>Social Links</h1>
</div>

<?php if ($error): ?>
    <div class="flash flash-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="flash flash-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<!-- Add New Link -->
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header">Add New Social Link</div>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
        <input type="hidden" name="action" value="add">
        <div class="form-row">
            <div class="form-group">
                <label>Platform</label>
                <select name="platform" required>
                    <option value="">Select platform...</option>
                    <?php foreach ($platforms as $key => $name): ?>
                        <option value="<?= $key ?>"><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>URL</label>
                <input type="url" name="url" placeholder="https://instagram.com/yourpage" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Link
        </button>
    </form>
</div>

<!-- Existing Links -->
<div class="table-wrap">
    <div class="table-header">
        <h2>Current Links</h2>
    </div>
    <?php if (empty($links)): ?>
        <div class="empty-state">
            <svg viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
            <p>No social links yet. Add one above!</p>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Platform</th><th>URL</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($links as $link): ?>
                <tr>
                    <td style="font-weight:500;"><?= htmlspecialchars($platforms[$link['platform']] ?? $link['platform']) ?></td>
                    <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        <a href="<?= htmlspecialchars($link['url']) ?>" target="_blank" rel="noopener" style="color:var(--accent);"><?= htmlspecialchars($link['url']) ?></a>
                    </td>
                    <td>
                        <span class="badge <?= $link['is_active'] ? 'badge-active' : 'badge-inactive' ?>">
                            <?= $link['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td class="actions">
                        <form class="inline" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="id" value="<?= $link['id'] ?>">
                            <a href="#" onclick="this.closest('form').requestSubmit(); return false;">
                                <?= $link['is_active'] ? 'Disable' : 'Enable' ?>
                            </a>
                        </form>
                        <form class="inline" method="POST" onsubmit="return confirm('Delete this link?');">
                            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $link['id'] ?>">
                            <a href="#" onclick="this.closest('form').requestSubmit(); return false;" class="danger-link">Delete</a>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
