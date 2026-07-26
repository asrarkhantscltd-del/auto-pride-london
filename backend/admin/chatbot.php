<?php
$pageTitle = 'AI Chatbot';
require __DIR__ . '/_layout_top.php';

$success = null;

// Handle settings save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'settings') {
        $enabled = isset($_POST['chatbot_enabled']) ? '1' : '0';
        $greeting = trim($_POST['chatbot_greeting'] ?? '');
        setSetting('chatbot_enabled', $enabled);
        setSetting('chatbot_greeting', $greeting);
        $success = 'Chatbot settings saved.';
    } elseif ($action === 'toggle_faq') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = db()->prepare('UPDATE chatbot_faqs SET is_active = NOT is_active WHERE id = ?');
            $stmt->execute([$id]);
            $success = 'FAQ toggled.';
        }
    } elseif ($action === 'delete_faq') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = db()->prepare('DELETE FROM chatbot_faqs WHERE id = ?');
            $stmt->execute([$id]);
            $success = 'FAQ deleted.';
        }
    }
}

$s = getAllSettings();
$faqs = db()->query('SELECT * FROM chatbot_faqs ORDER BY sort_order ASC, id ASC')->fetchAll();
?>

<div class="page-header">
    <h1>AI Chatbot</h1>
</div>

<?php if ($success): ?>
    <div class="flash flash-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (!empty($_GET['flash'])): ?>
    <div class="flash flash-success"><?= htmlspecialchars($_GET['flash']) ?></div>
<?php endif; ?>

<!-- Chatbot Settings -->
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
    <input type="hidden" name="action" value="settings">

    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header">Chatbot Settings</div>

        <div class="form-group" style="display:flex;align-items:center;gap:12px;margin-bottom:1.25rem;">
            <label class="toggle" style="margin-bottom:0;">
                <input type="checkbox" name="chatbot_enabled" value="1" <?= ($s['chatbot_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
                <span class="toggle-slider"></span>
            </label>
            <span style="font-size:0.9rem;">Enable chatbot widget on website</span>
        </div>

        <div class="form-group">
            <label>Greeting Message</label>
            <textarea name="chatbot_greeting" rows="2" placeholder="Hi! How can I help you?"><?= htmlspecialchars($s['chatbot_greeting'] ?? '') ?></textarea>
            <div class="form-hint">First message visitors see when they open the chat.</div>
        </div>

        <button type="submit" class="btn btn-primary btn-sm" style="margin-top:0.5rem;">
            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            Save Settings
        </button>
    </div>
</form>

<!-- FAQ Entries -->
<div class="page-header" style="margin-top:0.5rem;">
    <h2 style="font-size:1.1rem;">FAQ Entries</h2>
    <a class="btn btn-primary btn-sm" href="chatbot_faq_form.php">
        <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add FAQ
    </a>
</div>

<div class="form-hint" style="margin-bottom:1rem;">
    When a visitor asks a question, the chatbot matches their words against the keywords below to find the best answer.
</div>

<?php if (empty($faqs)): ?>
    <div class="card">
        <div class="empty-state">
            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <p>No FAQ entries yet. Add questions the chatbot can answer!</p>
            <a class="btn btn-primary btn-sm" href="chatbot_faq_form.php">Add FAQ</a>
        </div>
    </div>
<?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Question</th><th>Category</th><th>Keywords</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($faqs as $faq): ?>
                <tr>
                    <td style="font-weight:500;max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        <?= htmlspecialchars($faq['question']) ?>
                    </td>
                    <td><?= htmlspecialchars($faq['category']) ?></td>
                    <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text-muted);font-size:0.8rem;">
                        <?= htmlspecialchars($faq['keywords'] ?? '') ?>
                    </td>
                    <td>
                        <span class="badge <?= $faq['is_active'] ? 'badge-active' : 'badge-inactive' ?>">
                            <?= $faq['is_active'] ? 'Active' : 'Hidden' ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="chatbot_faq_form.php?id=<?= $faq['id'] ?>">Edit</a>
                        <form class="inline" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                            <input type="hidden" name="action" value="toggle_faq">
                            <input type="hidden" name="id" value="<?= $faq['id'] ?>">
                            <a href="#" onclick="this.closest('form').requestSubmit(); return false;">
                                <?= $faq['is_active'] ? 'Hide' : 'Show' ?>
                            </a>
                        </form>
                        <form class="inline" method="POST" onsubmit="return confirm('Delete this FAQ?');">
                            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                            <input type="hidden" name="action" value="delete_faq">
                            <input type="hidden" name="id" value="<?= $faq['id'] ?>">
                            <a href="#" onclick="this.closest('form').requestSubmit(); return false;" class="danger-link">Delete</a>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
