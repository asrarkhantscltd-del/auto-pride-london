<?php
$pageTitle = 'Feature Toggles';
require __DIR__ . '/_layout_top.php';

$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $allFeatures = [
        'hero_section', 'stats_section', 'testimonials_section',
        'whatsapp_button', 'chatbot_widget', 'products_page', 'inquiry_form',
    ];

    foreach ($allFeatures as $feature) {
        $enabled = isset($_POST[$feature]);
        setFeatureToggle($feature, $enabled);
    }

    $success = 'Feature toggles saved.';
}

$toggles = getAllFeatureToggles();

$features = [
    'hero_section' => [
        'name' => 'Hero Section',
        'desc' => 'Full-width hero banner with title, subtitle, and CTA buttons on the homepage.',
    ],
    'stats_section' => [
        'name' => 'Why Choose Us Stats',
        'desc' => 'Animated counter section showing cars sold, happy customers, and years in business.',
    ],
    'testimonials_section' => [
        'name' => 'Customer Testimonials',
        'desc' => 'Star ratings and customer reviews section on the homepage.',
    ],
    'whatsapp_button' => [
        'name' => 'WhatsApp Button',
        'desc' => 'Floating green WhatsApp chat button in the bottom-right corner.',
    ],
    'chatbot_widget' => [
        'name' => 'AI Chatbot',
        'desc' => 'FAQ-based chat assistant widget for visitor questions.',
    ],
    'products_page' => [
        'name' => 'Car Products Page',
        'desc' => 'The car accessories/products listing page in the navigation.',
    ],
    'inquiry_form' => [
        'name' => 'Enquiry Form',
        'desc' => 'Contact enquiry form on the homepage.',
    ],
];
?>

<div class="page-header">
    <h1>Feature Toggles</h1>
</div>

<?php if ($success): ?>
    <div class="flash flash-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

    <div class="card" style="padding:0;">
        <div class="card-header" style="padding:1rem 1.25rem;margin-bottom:0;">Toggle Frontend Features</div>

        <?php foreach ($features as $key => $info): ?>
            <div class="feature-row">
                <div class="feature-info">
                    <div class="feature-name"><?= htmlspecialchars($info['name']) ?></div>
                    <div class="feature-desc"><?= htmlspecialchars($info['desc']) ?></div>
                </div>
                <label class="toggle">
                    <input type="checkbox" name="<?= $key ?>" value="1" <?= ($toggles[$key] ?? true) ? 'checked' : '' ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="submit" class="btn btn-primary" style="margin-top:1.25rem;">
        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        Save Toggles
    </button>
</form>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
