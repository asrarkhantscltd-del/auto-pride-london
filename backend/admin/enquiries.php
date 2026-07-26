<?php
$pageTitle = 'Enquiries';
require __DIR__ . '/_layout_top.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete_id'])) {
    $stmt = db()->prepare('DELETE FROM enquiries WHERE id = ?');
    $stmt->execute([$_POST['delete_id']]);
    header('Location: enquiries.php');
    exit;
}

if (!empty($_GET['mark_read'])) {
    $stmt = db()->prepare('UPDATE enquiries SET is_read = 1 WHERE id = ?');
    $stmt->execute([$_GET['mark_read']]);
    header('Location: enquiries.php');
    exit;
}

$enquiries = db()->query('SELECT * FROM enquiries ORDER BY id DESC')->fetchAll();
?>

<h1>Enquiries</h1>

<table>
    <tr>
        <th>Received</th><th>Name</th><th>Email</th><th>Car Interest</th><th>Message</th><th></th>
    </tr>
    <?php foreach ($enquiries as $enquiry): ?>
    <tr style="<?= $enquiry['is_read'] ? 'opacity:0.6;' : '' ?>">
        <td><?= htmlspecialchars($enquiry['created_at']) ?></td>
        <td>
            <?php if (!$enquiry['is_read']): ?>
                <span style="color:#00FFCC;">●</span>
            <?php endif; ?>
            <?= htmlspecialchars($enquiry['name']) ?>
        </td>
        <td><a href="mailto:<?= htmlspecialchars($enquiry['email']) ?>" style="color:#fff;"><?= htmlspecialchars($enquiry['email']) ?></a></td>
        <td><?= htmlspecialchars($enquiry['car_interest'] ?: '—') ?></td>
        <td style="max-width:300px;white-space:normal;"><?= nl2br(htmlspecialchars($enquiry['message'])) ?></td>
        <td class="actions">
            <?php if (!$enquiry['is_read']): ?>
                <a href="?mark_read=<?= $enquiry['id'] ?>">Mark read</a>
            <?php endif; ?>
            <form class="inline" method="POST" onsubmit="return confirm('Delete this enquiry?');">
                <input type="hidden" name="delete_id" value="<?= $enquiry['id'] ?>">
                <a href="#" onclick="this.closest('form').requestSubmit(); return false;" style="color:#f87171;">Delete</a>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($enquiries)): ?>
    <tr><td colspan="6" style="color:#666;">No enquiries yet.</td></tr>
    <?php endif; ?>
</table>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
