<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POST only']);
    exit;
}

require __DIR__ . '/../config.php';
require __DIR__ . '/../helpers.php';

$input = json_decode(file_get_contents('php://input'), true);
$message = strtolower(trim($input['message'] ?? ''));

if ($message === '') {
    echo json_encode(['answer' => '', 'suggestions' => []]);
    exit;
}

$msgWords = preg_split('/[\s,.\-!?\'\"]+/', $message);
$msgWords = array_filter($msgWords, fn($w) => strlen($w) > 1);

$faqs = db()->query('SELECT * FROM chatbot_faqs WHERE is_active = 1 ORDER BY sort_order ASC')->fetchAll();

$bestScore = 0;
$bestFaq = null;

foreach ($faqs as $faq) {
    $keywords = array_filter(array_map('trim', explode(',', strtolower($faq['keywords'] ?? ''))));
    if (empty($keywords)) continue;

    $matches = 0;
    foreach ($keywords as $kw) {
        foreach ($msgWords as $word) {
            if ($word === $kw || str_contains($word, $kw) || str_contains($kw, $word)) {
                $matches++;
                break;
            }
        }
    }

    if ($matches > $bestScore) {
        $bestScore = $matches;
        $bestFaq = $faq;
    }
}

$suggestions = array_slice(array_column($faqs, 'question'), 0, 4);

if ($bestFaq && $bestScore >= 1) {
    echo json_encode([
        'answer' => $bestFaq['answer'],
        'suggestions' => $suggestions,
    ]);
} else {
    $phone = getSetting('phone', '+44 7771 406382');
    echo json_encode([
        'answer' => "I'm not sure about that. Please contact us directly at {$phone} or use the enquiry form, and we'll be happy to help!",
        'suggestions' => $suggestions,
    ]);
}
