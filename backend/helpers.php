<?php
require_once __DIR__ . '/config.php';

function getSetting(string $key, ?string $default = null): ?string
{
    static $cache = [];
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }
    $stmt = db()->prepare('SELECT setting_value FROM site_settings WHERE setting_key = ?');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    $value = $row ? $row['setting_value'] : $default;
    $cache[$key] = $value;
    return $value;
}

function setSetting(string $key, ?string $value): void
{
    $driver = db()->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'mysql') {
        $stmt = db()->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    } else {
        $stmt = db()->prepare('INSERT OR REPLACE INTO site_settings (setting_key, setting_value) VALUES (?, ?)');
    }
    $stmt->execute([$key, $value]);
}

function getAllSettings(): array
{
    $rows = db()->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll();
    $settings = [];
    foreach ($rows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

function getFeatureToggle(string $key): bool
{
    $stmt = db()->prepare('SELECT is_enabled FROM feature_toggles WHERE feature_key = ?');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? (bool) $row['is_enabled'] : true;
}

function setFeatureToggle(string $key, bool $enabled): void
{
    $driver = db()->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'mysql') {
        $stmt = db()->prepare('INSERT INTO feature_toggles (feature_key, is_enabled) VALUES (?, ?) ON DUPLICATE KEY UPDATE is_enabled = VALUES(is_enabled)');
    } else {
        $stmt = db()->prepare('INSERT OR REPLACE INTO feature_toggles (feature_key, is_enabled) VALUES (?, ?)');
    }
    $stmt->execute([$key, $enabled ? 1 : 0]);
}

function getAllFeatureToggles(): array
{
    $rows = db()->query('SELECT feature_key, is_enabled FROM feature_toggles')->fetchAll();
    $toggles = [];
    foreach ($rows as $row) {
        $toggles[$row['feature_key']] = (bool) $row['is_enabled'];
    }
    return $toggles;
}
