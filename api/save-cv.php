<?php
// api/save-cv.php - Save CV Data
require_once '../config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No CV data received']);
    exit;
}

// Log CV creation
$logData = [
    'name' => $data['name'] ?? 'Unknown',
    'email' => $data['email'] ?? 'No email',
    'timestamp' => date('Y-m-d H:i:s')
];

$logMessage = json_encode($logData) . PHP_EOL;
file_put_contents('../logs/cv_creation.log', $logMessage, FILE_APPEND);

// Send email notification to admin
$subject = "New CV Created - AI CV Creator";
$message = "A new CV has been created:\n\n";
$message .= "Name: " . ($data['name'] ?? 'N/A') . "\n";
$message .= "Email: " . ($data['email'] ?? 'N/A') . "\n";
$message .= "Profession: " . ($data['profession'] ?? 'N/A') . "\n";
$message .= "Time: " . date('Y-m-d H:i:s') . "\n";

if (function_exists('sendEmail')) {
    sendEmail(ADMIN_EMAIL_PRIMARY, $subject, $message);
}

echo json_encode(['success' => true, 'message' => 'CV saved successfully', 'timestamp' => date('Y-m-d H:i:s')]);
?>
