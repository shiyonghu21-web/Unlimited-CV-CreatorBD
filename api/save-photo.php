<?php
// api/save-photo.php - Handle Photo Uploads
require_once '../config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_FILES['photo'])) {
    echo json_encode(['success' => false, 'message' => 'No photo uploaded']);
    exit;
}

$photo = $_FILES['photo'];
$userName = $_POST['userName'] ?? 'Unknown User';

// Validate file
if ($photo['size'] > MAX_FILE_SIZE) {
    echo json_encode(['success' => false, 'message' => 'File too large']);
    exit;
}

if (!in_array($photo['type'], ALLOWED_FILE_TYPES)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type']);
    exit;
}

// Save photo
$filename = time() . '_' . preg_replace('/[^a-z0-9\.]/i', '', $photo['name']);
$filepath = PHOTO_UPLOAD_DIR . $filename;

if (move_uploaded_file($photo['tmp_name'], $filepath)) {
    // Log photo upload
    $logData = [
        'user' => $userName,
        'filename' => $filename,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    file_put_contents('../logs/photo_uploads.log', json_encode($logData) . PHP_EOL, FILE_APPEND);
    
    // Send email notification
    $subject = "New Photo Upload - AI CV Creator";
    $message = "User: $userName\n";
    $message .= "Filename: $filename\n";
    $message .= "Time: " . date('Y-m-d H:i:s');
    
    if (function_exists('sendEmail')) {
        sendEmail(ADMIN_EMAIL_PRIMARY, $subject, $message);
    }
    
    echo json_encode(['success' => true, 'message' => 'Photo uploaded successfully', 'filename' => $filename]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save photo']);
}
?>
