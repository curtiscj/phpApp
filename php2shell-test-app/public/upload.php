<?php
/**
 * INTENTIONALLY VULNERABLE — do not copy into real code.
 *
 * Unrestricted file upload (PHP2Shell pattern):
 *  - no extension allow-list
 *  - no MIME/content validation
 *  - no filename sanitization
 *  - stored directly under the webroot, where PHP execution is enabled
 *
 * This lets an attacker upload a .php webshell and then execute it by
 * requesting it directly, i.e. unauthenticated remote code execution.
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['file'])) {
    http_response_code(400);
    echo 'POST a file using the "file" field.';
    exit;
}

$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// VULNERABLE: original filename used as-is, no extension/type checks.
$destination = $uploadDir . basename($_FILES['file']['name']);

if (move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
    echo "Uploaded to: uploads/" . basename($_FILES['file']['name']) . "\n";
} else {
    http_response_code(500);
    echo "Upload failed.\n";
}
