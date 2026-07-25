<?php

/**
 * Handles an uploaded image file and returns the public path to store in the
 * database, or null if no file was uploaded. Throws RuntimeException on
 * invalid uploads (wrong type, too large, etc).
 */
function handleImageUpload(string $fieldName): ?string
{
    if (empty($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES[$fieldName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed (error code ' . $file['error'] . ').');
    }

    $maxBytes = 5 * 1024 * 1024; // 5 MB
    if ($file['size'] > $maxBytes) {
        throw new RuntimeException('Image is too large (max 5 MB).');
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!isset($allowed[$mimeType])) {
        throw new RuntimeException('Only JPG, PNG, WEBP, or GIF images are allowed.');
    }

    $extension = $allowed[$mimeType];
    $filename = bin2hex(random_bytes(8)) . '.' . $extension;
    $uploadsDir = __DIR__ . '/uploads';

    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
    }

    $destination = $uploadsDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Could not save the uploaded file.');
    }

    return '/backend/uploads/' . $filename;
}
