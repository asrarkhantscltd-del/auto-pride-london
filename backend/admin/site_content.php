<?php
$pageTitle = 'Site Content';
require __DIR__ . '/_layout_top.php';
require __DIR__ . '/../upload.php';

$success = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $fields = [
        'business_name', 'tagline', 'phone', 'email', 'address',
        'hero_title', 'hero_subtitle', 'hero_background_video',
        'stats_cars_sold', 'stats_happy_customers', 'stats_years_in_business',
    ];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            setSetting($field, trim($_POST[$field]));
        }
    }

    try {
        $heroImage = handleImageUpload('hero_background_image');
        if ($heroImage !== null) {
            setSetting('hero_background_image', $heroImage);
        }
    } catch (RuntimeException $e) {
        $error = 'Hero image: ' . $e->getMessage();
    }

    if (!$error) {
        try {
            $heroVideo = handleVideoUpload('hero_background_video');
            if ($heroVideo !== null) {
                setSetting('hero_background_video', $heroVideo);
            }
        } catch (RuntimeException $e) {
            $error = 'Hero video: ' . $e->getMessage();
        }
    }

    if (!$error) {
        $success = 'Site content updated successfully.';
    }
}

$s = getAllSettings();
?>

<div class="page-header">
    <h1>Site Content</h1>
</div>

<?php if ($error): ?>
    <div class="flash flash-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="flash flash-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

    <!-- Business Information -->
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header">Business Information</div>

        <div class="form-row">
            <div class="form-group">
                <label>Business Name</label>
                <input type="text" name="business_name" value="<?= htmlspecialchars($s['business_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Tagline</label>
                <input type="text" name="tagline" value="<?= htmlspecialchars($s['tagline'] ?? '') ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($s['phone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($s['email'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Address / Location</label>
            <input type="text" name="address" value="<?= htmlspecialchars($s['address'] ?? '') ?>">
        </div>
    </div>

    <!-- Hero Section -->
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header">Hero Section</div>

        <div class="form-group">
            <label>Hero Title</label>
            <input type="text" name="hero_title" value="<?= htmlspecialchars($s['hero_title'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Hero Subtitle</label>
            <textarea name="hero_subtitle" rows="2"><?= htmlspecialchars($s['hero_subtitle'] ?? '') ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Hero Background Image</label>
                <input type="file" name="hero_background_image" accept="image/*">
                <?php if (!empty($s['hero_background_image'])): ?>
                    <img src="<?= htmlspecialchars($s['hero_background_image']) ?>" class="img-preview" alt="Current hero image">
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>Hero Background Video (MP4, WEBM, MOV — max 50 MB)</label>
                <input type="file" name="hero_background_video" accept="video/mp4,video/webm,video/quicktime,.mp4,.webm,.mov">
                <?php if (!empty($s['hero_background_video'])): ?>
                    <div style="margin-top:0.5rem;font-size:0.8rem;color:var(--accent);">Current: <?= htmlspecialchars(basename($s['hero_background_video'])) ?></div>
                <?php endif; ?>
                <div class="form-hint">Optional. Video plays behind the hero text. Leave empty to keep image-only.</div>
            </div>
        </div>
    </div>

    <!-- Why Choose Us Stats -->
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header">Why Choose Us — Stats</div>
        <div class="form-hint" style="margin-bottom:1rem;">These numbers appear in the "Why Choose Us" section on the homepage.</div>

        <div class="form-row">
            <div class="form-group">
                <label>Cars Sold</label>
                <input type="number" name="stats_cars_sold" value="<?= htmlspecialchars($s['stats_cars_sold'] ?? '0') ?>" min="0">
            </div>
            <div class="form-group">
                <label>Happy Customers</label>
                <input type="number" name="stats_happy_customers" value="<?= htmlspecialchars($s['stats_happy_customers'] ?? '0') ?>" min="0">
            </div>
        </div>

        <div class="form-group" style="max-width:calc(50% - 0.5rem);">
            <label>Years in Business</label>
            <input type="number" name="stats_years_in_business" value="<?= htmlspecialchars($s['stats_years_in_business'] ?? '0') ?>" min="0">
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        Save Changes
    </button>
</form>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
