<?php
$editId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$pageTitle = $editId ? 'Edit FAQ' : 'Add FAQ';
require __DIR__ . '/_layout_top.php';

$faq = ['question' => '', 'answer' => '', 'keywords' => '', 'category' => 'General', 'sort_order' => 0];
$error = null;

if ($editId) {
    $stmt = db()->prepare('SELECT * FROM chatbot_faqs WHERE id = ?');
    $stmt->execute([$editId]);
    $found = $stmt->fetch();
    if (!$found) {
        header('Location: chatbot.php?flash=' . urlencode('FAQ not found.'));
        exit;
    }
    $faq = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $question = trim($_POST['question'] ?? '');
    $answer = trim($_POST['answer'] ?? '');
    $keywords = trim($_POST['keywords'] ?? '');
    $category = trim($_POST['category'] ?? 'General');
    $sortOrder = (int) ($_POST['sort_order'] ?? 0);

    if ($question === '' || $answer === '') {
        $error = 'Question and answer are required.';
    } elseif ($keywords === '') {
        $error = 'Add at least one keyword so the chatbot can match this FAQ.';
    } else {
        if ($editId) {
            $stmt = db()->prepare('UPDATE chatbot_faqs SET question = ?, answer = ?, keywords = ?, category = ?, sort_order = ? WHERE id = ?');
            $stmt->execute([$question, $answer, $keywords, $category, $sortOrder, $editId]);
            header('Location: chatbot.php?flash=' . urlencode('FAQ updated.'));
        } else {
            $stmt = db()->prepare('INSERT INTO chatbot_faqs (question, answer, keywords, category, sort_order) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$question, $answer, $keywords, $category, $sortOrder]);
            header('Location: chatbot.php?flash=' . urlencode('FAQ added.'));
        }
        exit;
    }

    $faq = ['question' => $question, 'answer' => $answer, 'keywords' => $keywords, 'category' => $category, 'sort_order' => $sortOrder];
}

$categories = ['General', 'Cars', 'Finance', 'Location', 'Delivery', 'Products', 'Contact', 'Hours'];
?>

<div class="page-header">
    <h1><?= $editId ? 'Edit' : 'Add' ?> FAQ Entry</h1>
    <a href="chatbot.php" class="btn btn-secondary btn-sm">Back to Chatbot</a>
</div>

<?php if ($error): ?>
    <div class="flash flash-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

    <div class="card" style="max-width:650px;">
        <div class="form-group">
            <label>Question</label>
            <input type="text" name="question" value="<?= htmlspecialchars($faq['question']) ?>" required placeholder="e.g. Do you offer finance?">
            <div class="form-hint">The exact question shown to visitors as a suggestion.</div>
        </div>

        <div class="form-group">
            <label>Answer</label>
            <textarea name="answer" rows="4" required placeholder="The chatbot's response..."><?= htmlspecialchars($faq['answer']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Keywords (comma-separated)</label>
            <input type="text" name="keywords" value="<?= htmlspecialchars($faq['keywords']) ?>" required placeholder="e.g. finance,payment,loan,monthly,credit">
            <div class="form-hint">Words the chatbot looks for in the visitor's message to find this answer. More keywords = better matching.</div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Category</label>
                <select name="category">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat ?>" <?= $faq['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" value="<?= (int) $faq['sort_order'] ?>" min="0">
                <div class="form-hint">Lower = appears first in suggestions.</div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top:0.5rem;">
            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            <?= $editId ? 'Update' : 'Add' ?> FAQ
        </button>
    </div>
</form>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
