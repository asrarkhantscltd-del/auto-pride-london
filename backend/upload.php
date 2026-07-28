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

    $allowed = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_WEBP => 'webp',
        IMAGETYPE_GIF => 'gif',
    ];

    $imageInfo = @getimagesize($file['tmp_name']);

    if ($imageInfo === false || !isset($allowed[$imageInfo[2]])) {
        throw new RuntimeException('Only JPG, PNG, WEBP, or GIF images are allowed.');
    }

    $extension = $allowed[$imageInfo[2]];
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

/**
 * Handles multiple uploaded image files (from a `name="field[]" multiple`
 * input) and returns an array of public paths for the successfully saved
 * ones. Files that fail validation are silently skipped rather than
 * aborting the whole batch.
 */
function handleVideoUpload(string $fieldName): ?string
{
    if (empty($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES[$fieldName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed (error code ' . $file['error'] . ').');
    }

    $allowedExts = ['mp4' => true, 'webm' => true, 'mov' => true];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!isset($allowedExts[$ext])) {
        throw new RuntimeException('Only MP4, WEBM, or MOV videos are allowed.');
    }

    $filename = bin2hex(random_bytes(8)) . '.' . $ext;
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

function handleMultipleImageUploads(string $fieldName): array
{
    if (empty($_FILES[$fieldName]) || !is_array($_FILES[$fieldName]['name'])) {
        return [];
    }

    $count = count($_FILES[$fieldName]['name']);
    $saved = [];

    for ($i = 0; $i < $count; $i++) {
        if ($_FILES[$fieldName]['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        $singleFile = [
            'name' => $_FILES[$fieldName]['name'][$i],
            'type' => $_FILES[$fieldName]['type'][$i],
            'tmp_name' => $_FILES[$fieldName]['tmp_name'][$i],
            'error' => $_FILES[$fieldName]['error'][$i],
            'size' => $_FILES[$fieldName]['size'][$i],
        ];

        $originalFiles = $_FILES[$fieldName];
        $_FILES[$fieldName] = $singleFile;

        try {
            $path = handleImageUpload($fieldName);
            if ($path !== null) {
                $saved[] = $path;
            }
        } catch (RuntimeException $e) {
            // Skip invalid files in a batch rather than failing the whole upload.
        } finally {
            $_FILES[$fieldName] = $originalFiles;
        }
    }

    return $saved;
}
