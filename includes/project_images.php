<?php
/**
 * Project image storage. New images are uploaded to Cloudinary.
 * CLOUDINARY_URL must be configured on the server, never committed to Git.
 */
function projectImageUrl($image) {
    $image = trim((string) $image);
    if ($image === '') return '';
    if (preg_match('~^https://res\\.cloudinary\\.com/[a-zA-Z0-9_-]+/image/upload/~', $image)) {
        return $image;
    }
    $name = basename($image);
    if (is_file(__DIR__ . '/../uploads/' . $name)) return '/uploads/' . rawurlencode($name);
    if (is_file(__DIR__ . '/../images/' . $name)) return '/images/' . rawurlencode($name);
    return '';
}

function uploadProjectImageToCloudinary($file) {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Image upload failed. Please try again.');
    }
    if ($file['size'] > 10 * 1024 * 1024 || $file['size'] <= 0) {
        throw new RuntimeException('Image must be smaller than 10 MB.');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/avif'], true)) {
        throw new RuntimeException('Only JPG, PNG, WEBP and AVIF images are allowed.');
    }
    $cloudinaryUrl = getenv('CLOUDINARY_URL');
    if (!$cloudinaryUrl) throw new RuntimeException('Cloudinary is not configured on the server.');
    $config = parse_url($cloudinaryUrl);
    if (!$config || ($config['scheme'] ?? '') !== 'cloudinary' || empty($config['user']) || empty($config['pass']) || empty($config['host'])) {
        throw new RuntimeException('Invalid Cloudinary configuration.');
    }
    if (!function_exists('curl_init')) throw new RuntimeException('PHP cURL extension is required.');
    $timestamp = time();
    $folder = 'felix-projects';
    $signature = sha1('folder=' . $folder . '&timestamp=' . $timestamp . $config['pass']);
    $endpoint = 'https://api.cloudinary.com/v1_1/' . rawurlencode($config['host']) . '/image/upload';
    $post = [
        'file' => new CURLFile($file['tmp_name'], $mime, basename($file['name'])),
        'api_key' => $config['user'],
        'timestamp' => $timestamp,
        'folder' => $folder,
        'signature' => $signature,
    ];
    $curl = curl_init($endpoint);
    curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $post,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 60,
    ]);
    $response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curlError = curl_error($curl);
    curl_close($curl);
    $data = is_string($response) ? json_decode($response, true) : null;
    if ($response === false || $status < 200 || $status >= 300 || empty($data['secure_url'])) {
        error_log('Cloudinary upload error: HTTP ' . $status . ' ' . $curlError);
        throw new RuntimeException('Could not save image to Cloudinary. Please try again.');
    }
    return $data['secure_url'];
}
