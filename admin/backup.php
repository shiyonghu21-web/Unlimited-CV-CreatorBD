<?php
// admin/backup.php - Database Backup
require_once '../config.php';
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin.php');
    exit;
}

// Create backup directory if not exists
if (!file_exists(DB_BACKUP_PATH)) {
    mkdir(DB_BACKUP_PATH, 0777, true);
}

// Create backup filename
$backupFile = DB_BACKUP_PATH . 'backup_' . date('Y-m-d_H-i-s') . '.accdb';

// Copy database file
if (copy(DB_PATH, $backupFile)) {
    // Log backup
    $logMessage = date('Y-m-d H:i:s') . " - Backup created: " . basename($backupFile) . "\n";
    file_put_contents('../logs/backup.log', $logMessage, FILE_APPEND);
    
    header('Location: admin.php?success=backup_created');
} else {
    header('Location: admin.php?error=backup_failed');
}
exit;
?>
