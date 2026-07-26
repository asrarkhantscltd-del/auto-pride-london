<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require __DIR__ . '/../config.php';
require __DIR__ . '/../helpers.php';

$settings = getAllSettings();
$toggles = getAllFeatureToggles();
$socialLinks = db()->query('SELECT platform, url FROM social_links WHERE is_active = 1 ORDER BY display_order ASC')->fetchAll();

echo json_encode([
    'site' => [
        'business_name' => $settings['business_name'] ?? 'Auto Pride London',
        'tagline' => $settings['tagline'] ?? '',
        'phone' => $settings['phone'] ?? '',
        'email' => $settings['email'] ?? '',
        'address' => $settings['address'] ?? '',
    ],
    'hero' => [
        'title' => $settings['hero_title'] ?? 'Auto Pride London',
        'subtitle' => $settings['hero_subtitle'] ?? '',
        'background_image' => $settings['hero_background_image'] ?? '',
        'background_video' => $settings['hero_background_video'] ?? '',
    ],
    'stats' => [
        'cars_sold' => (int) ($settings['stats_cars_sold'] ?? 0),
        'happy_customers' => (int) ($settings['stats_happy_customers'] ?? 0),
        'years_in_business' => (int) ($settings['stats_years_in_business'] ?? 0),
    ],
    'whatsapp' => [
        'enabled' => ($settings['whatsapp_enabled'] ?? '1') === '1',
        'phone' => $settings['whatsapp_phone'] ?? '',
        'message' => $settings['whatsapp_message'] ?? '',
    ],
    'chatbot' => [
        'enabled' => ($settings['chatbot_enabled'] ?? '1') === '1',
        'greeting' => $settings['chatbot_greeting'] ?? 'Hi! How can I help you?',
    ],
    'social_links' => $socialLinks,
    'features' => $toggles,
]);
