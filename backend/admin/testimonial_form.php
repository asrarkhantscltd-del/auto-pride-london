<?php
require __DIR__ . '/../upload.php';

$editId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$pageTitle = $editId ? 'Edit Testimonial' : 'Add Testimonial';
require __DIR__ . '/_layout_top.php';

$testimonial = ['customer_name' => '', 'review_text' => '', 'rating' => 5, 'photo' => null, 'sort_order' => 0];
$error = null;

if ($editId) {
    $stmt = db()->prepare('SELECT * FROM testimonials WHERE id = ?');
    $stmt->execute([$editId]);
    $found = $stmt->fetch();
    if (!$found) {
        header('Location: testimonials.php?flash=' . urlencode('Testimonial not found.'));
        exit;
    }
    $testimonial = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $name = trim($_POST['customer_name'] ?? '');
    $text = trim($_POST['review_text'] ?? '');
    $rating = max(1, min(5, (int) ($_POST['rating'] ?? 5)));
    $sortOrder = (int) ($_POST['sort_order'] ?? 0);

    if ($name === '' || $text === '') {
        $error = 'Customer name and review text are required.';
    } else {
        $photo = $testimonial['photo'];
        try {
            $uploaded = handleImageUpload('photo');
            if ($uploaded !== null) {
                $photo = $uploaded;
            }
        } catch (RuntimeException $e) {
            $error = 'Photo: ' . $e->getMessage();
        }

        if (!$error) {
            if ($editId) {
                $stmt = db()->prepare('UPDATE testimonials SET customer_name = ?, review_text = ?, rating = ?, photo = ?, sort_order = ? WHERE id = ?');
                $stmt->execute([$name, $text, $rating, $photo, $sortOrder, $editId]);
                header('Location: testimonials.php?flash=' . urlencode('Testimonial updated.'));
            } else {
                $stmt = db()->prepare('INSERT INTO testimonials (customer_name, review_text, rating, photo, sort_order) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$name, $text, $rating, $photo, $sortOrder]);
                header('Location: testimonials.php?flash=' . urlencode('Testimonial added.'));
            }
            exit;
        }
    }

    $testimonial = ['customer_name' => $name, 'review_text' => $text, 'rating' => $rating, 'photo' => $testimonial['photo'], 'sort_order' => $sortOrder];
}
?>

<div class="page-header">
    <h1><?= $editId ? 'Edit' : 'Add' ?> Testimonial</h1>
    <a href="testimonials.php" class="btn btn-secondary btn-sm">Back to List</a>
</div>

<?php if ($error): ?>
    <div class="flash flash-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

    <div class="card" style="max-width:600px;">
        <div class="form-group">
            <label>Customer Name</label>
            <input type="text" name="customer_name" value="<?= htmlspecialchars($testimonial['customer_name']) ?>" required placeholder="e.g. James R.">
        </div>

        <div class="form-group">
            <label>Review Text</label>
            <textarea name="review_text" rows="4" required placeholder="What did the customer say?"><?= htmlspecialchars($testimonial['review_text']) ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Rating</label>
                <select name="rating">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <option value="<?= $i ?>" <?= (int) $testimonial['rating'] === $i ? 'selected' : '' ?>><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" value="<?= (int) $testimonial['sort_order'] ?>" min="0">
                <div class="form-hint">Lower numbers appear first.</div>
            </div>
        </div>

        <div class="form-group">
            <label>Customer Photo (optional)</label>
            <input type="file" name="photo" accept="image/*">
            <?php if ($testimonial['photo']): ?>
                <img src="<?= htmlspecialchars($testimonial['photo']) ?>" style="width:60px;height:60px;border-radius:50%;object-fit:cover;margin-top:0.5rem;border:1px solid var(--border);" alt="">
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top:0.5rem;">
            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            <?= $editId ? 'Update' : 'Add' ?> Testimonial
        </button>
    </div>
</form>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
