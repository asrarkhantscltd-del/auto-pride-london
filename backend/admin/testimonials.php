<?php
$pageTitle = 'Testimonials';
require __DIR__ . '/_layout_top.php';

$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);

    if ($action === 'toggle' && $id > 0) {
        $stmt = db()->prepare('UPDATE testimonials SET is_active = NOT is_active WHERE id = ?');
        $stmt->execute([$id]);
        $success = 'Testimonial toggled.';
    } elseif ($action === 'delete' && $id > 0) {
        $row = db()->prepare('SELECT photo FROM testimonials WHERE id = ?');
        $row->execute([$id]);
        $t = $row->fetch();
        if ($t && $t['photo']) {
            $file = __DIR__ . '/../..' . $t['photo'];
            if (file_exists($file)) unlink($file);
        }
        $stmt = db()->prepare('DELETE FROM testimonials WHERE id = ?');
        $stmt->execute([$id]);
        $success = 'Testimonial deleted.';
    }
}

$testimonials = db()->query('SELECT * FROM testimonials ORDER BY sort_order ASC, id DESC')->fetchAll();
?>

<div class="page-header">
    <h1>Testimonials</h1>
    <a class="btn btn-primary btn-sm" href="testimonial_form.php">
        <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Testimonial
    </a>
</div>

<?php if ($success): ?>
    <div class="flash flash-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (!empty($_GET['flash'])): ?>
    <div class="flash flash-success"><?= htmlspecialchars($_GET['flash']) ?></div>
<?php endif; ?>

<?php if (empty($testimonials)): ?>
    <div class="card">
        <div class="empty-state">
            <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <p>No testimonials yet. Add your first customer review!</p>
            <a class="btn btn-primary btn-sm" href="testimonial_form.php">Add Testimonial</a>
        </div>
    </div>
<?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Customer</th><th>Rating</th><th>Review</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($testimonials as $t): ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <?php if ($t['photo']): ?>
                                <img src="<?= htmlspecialchars($t['photo']) ?>" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:1px solid var(--border);" alt="">
                            <?php else: ?>
                                <div style="width:36px;height:36px;border-radius:50%;background:var(--accent-dim);display:flex;align-items:center;justify-content:center;color:var(--accent);font-weight:600;font-size:0.85rem;">
                                    <?= strtoupper(substr($t['customer_name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <span style="font-weight:500;"><?= htmlspecialchars($t['customer_name']) ?></span>
                        </div>
                    </td>
                    <td>
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <svg class="star <?= $i <= $t['rating'] ? '' : 'empty' ?>" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            <?php endfor; ?>
                        </div>
                    </td>
                    <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        <?= htmlspecialchars(mb_substr($t['review_text'], 0, 80)) ?><?= mb_strlen($t['review_text']) > 80 ? '...' : '' ?>
                    </td>
                    <td>
                        <span class="badge <?= $t['is_active'] ? 'badge-active' : 'badge-inactive' ?>">
                            <?= $t['is_active'] ? 'Active' : 'Hidden' ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="testimonial_form.php?id=<?= $t['id'] ?>">Edit</a>
                        <form class="inline" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="id" value="<?= $t['id'] ?>">
                            <a href="#" onclick="this.closest('form').requestSubmit(); return false;">
                                <?= $t['is_active'] ? 'Hide' : 'Show' ?>
                            </a>
                        </form>
                        <form class="inline" method="POST" onsubmit="return confirm('Delete this testimonial?');">
                            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $t['id'] ?>">
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
