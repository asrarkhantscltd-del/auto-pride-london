<?php
$pageTitle = 'WhatsApp Settings';
require __DIR__ . '/_layout_top.php';

$success = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $phone = trim($_POST['whatsapp_phone'] ?? '');
    $message = trim($_POST['whatsapp_message'] ?? '');
    $enabled = isset($_POST['whatsapp_enabled']) ? '1' : '0';

    if ($phone === '') {
        $error = 'Phone number is required.';
    } else {
        setSetting('whatsapp_phone', $phone);
        setSetting('whatsapp_message', $message);
        setSetting('whatsapp_enabled', $enabled);
        $success = 'WhatsApp settings saved.';
    }
}

$s = getAllSettings();
?>

<div class="page-header">
    <h1>WhatsApp Settings</h1>
</div>

<?php if ($error): ?>
    <div class="flash flash-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="flash flash-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

    <div class="card" style="max-width:600px;">
        <div class="card-header">WhatsApp Chat Button</div>

        <div class="form-group" style="display:flex;align-items:center;gap:12px;margin-bottom:1.25rem;">
            <label class="toggle" style="margin-bottom:0;">
                <input type="checkbox" name="whatsapp_enabled" value="1" <?= ($s['whatsapp_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
                <span class="toggle-slider"></span>
            </label>
            <span style="font-size:0.9rem;">Enable floating WhatsApp button on website</span>
        </div>

        <hr class="form-divider">

        <div class="form-group">
            <label>Phone Number (international format, no + or spaces)</label>
            <input type="text" name="whatsapp_phone" value="<?= htmlspecialchars($s['whatsapp_phone'] ?? '') ?>" placeholder="447771406382">
            <div class="form-hint">Example: 447771406382 (country code + number, no spaces or dashes)</div>
        </div>

        <div class="form-group">
            <label>Default Message</label>
            <textarea name="whatsapp_message" rows="3" placeholder="Hi, I'm interested in..."><?= htmlspecialchars($s['whatsapp_message'] ?? '') ?></textarea>
            <div class="form-hint">Pre-filled message when visitors click the WhatsApp button.</div>
        </div>

        <div style="margin-top:1rem; padding:1rem; background:var(--bg-body); border-radius:var(--radius); border:1px solid var(--border);">
            <div style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.5rem;">Preview link:</div>
            <a id="waPreview" href="#" target="_blank" rel="noopener" style="font-size:0.85rem;color:var(--accent);word-break:break-all;"></a>
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top:1.25rem;">
            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            Save Settings
        </button>
    </div>
</form>

<script>
function updatePreview() {
    var phone = document.querySelector('[name="whatsapp_phone"]').value.trim();
    var msg = document.querySelector('[name="whatsapp_message"]').value.trim();
    var url = 'https://wa.me/' + phone + (msg ? '?text=' + encodeURIComponent(msg) : '');
    var el = document.getElementById('waPreview');
    el.href = url;
    el.textContent = url;
}
document.querySelector('[name="whatsapp_phone"]').addEventListener('input', updatePreview);
document.querySelector('[name="whatsapp_message"]').addEventListener('input', updatePreview);
updatePreview();
</script>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
