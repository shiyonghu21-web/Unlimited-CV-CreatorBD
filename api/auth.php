<?php
// api/auth.php - Authentication API
require_once '../config.php';
header('Content-Type: application/json');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

$action = $data['action'] ?? '';

switch ($action) {
    case 'verification':
        // Log verification
        $log = date('Y-m-d H:i:s') . " - " . ($data['type'] ?? 'unknown') . " verification for: " . 
               ($data['userData']['email'] ?? 'unknown') . "\n";
        file_put_contents('../logs/verifications.log', $log, FILE_APPEND);
        
        echo json_encode(['success' => true, 'message' => 'Verification logged']);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
}
?>
