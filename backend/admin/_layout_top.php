<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../auth.php';
require __DIR__ . '/../helpers.php';
requireAuth();

$currentPage = basename($_SERVER['SCRIPT_NAME']);
$unreadCount = (int) db()->query('SELECT COUNT(*) AS c FROM enquiries WHERE is_read = 0')->fetch()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — Auto Pride London</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<!-- Mobile Topbar -->
<div class="topbar">
    <button class="hamburger" onclick="toggleSidebar()" aria-label="Menu">
        <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <span class="topbar-title">Auto Pride London</span>
</div>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <a href="index.php">
            <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 22 4 22 16 16 12 16 8"/></svg>
            Auto Pride London
        </a>
    </div>

    <div class="sidebar-nav">
        <div class="nav-section">Main</div>

        <a href="index.php" class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard
        </a>

        <a href="car_form.php" class="nav-link <?= $currentPage === 'car_form.php' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24"><path d="M5 17h2m10 0h2M3 9l2-5h14l2 5"/><path d="M3 9h18v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"/><circle cx="7" cy="17" r="0.5"/><circle cx="17" cy="17" r="0.5"/></svg>
            Add Car
        </a>

        <a href="product_form.php" class="nav-link <?= $currentPage === 'product_form.php' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            Add Product
        </a>

        <a href="enquiries.php" class="nav-link <?= $currentPage === 'enquiries.php' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            Enquiries
            <?php if ($unreadCount > 0): ?>
                <span class="nav-badge"><?= $unreadCount ?></span>
            <?php endif; ?>
        </a>

        <div class="nav-section">Site Management</div>

        <a href="site_content.php" class="nav-link <?= $currentPage === 'site_content.php' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Site Content
        </a>

        <a href="social_links.php" class="nav-link <?= $currentPage === 'social_links.php' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
            Social Links
        </a>

        <a href="testimonials.php" class="nav-link <?= $currentPage === 'testimonials.php' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            Testimonials
        </a>

        <a href="whatsapp.php" class="nav-link <?= $currentPage === 'whatsapp.php' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
            WhatsApp Settings
        </a>

        <a href="chatbot.php" class="nav-link <?= $currentPage === 'chatbot.php' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            AI Chatbot
        </a>

        <a href="feature_toggles.php" class="nav-link <?= $currentPage === 'feature_toggles.php' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            Feature Toggles
        </a>

        <div class="nav-section">Account</div>

        <a href="settings.php" class="nav-link <?= $currentPage === 'settings.php' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Account Settings
        </a>

        <a href="logout.php" class="nav-link logout">
            <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Log Out
        </a>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content">
