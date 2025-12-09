<?php
// config.example.php - Example Configuration
// Copy this file to config.php and edit the values

// Site Configuration
define('SITE_URL', 'http://your-domain.com/cv_creator/');
define('SITE_NAME', 'AI CV Creator');

// Database Configuration
define('DB_PATH', 'C:/inetpub/wwwroot/cv_creator/database/cv_users.accdb');
define('DB_BACKUP_PATH', 'C:/inetpub/wwwroot/cv_creator/database/backups/');

// Email Configuration
define('ADMIN_EMAIL_PRIMARY', 'shiyonghu21@gmail.com');
define('ADMIN_EMAIL_SECONDARY', 'ontimebyshihab@outlook.com');
define('EMAIL_FROM', 'noreply@yourdomain.com');
define('EMAIL_FROM_NAME', 'AI CV Creator');

// File Upload Configuration
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']);
define('UPLOAD_DIR', 'C:/inetpub/wwwroot/cv_creator/uploads/');
define('PHOTO_UPLOAD_DIR', UPLOAD_DIR . 'photos/');

// Verification Settings
define('FREE_CV_LIMIT', 5);

// Security Settings
define('SESSION_TIMEOUT', 3600);
define('ADMIN_PASSWORD', 'CHANGE_THIS_PASSWORD'); // CHANGE THIS!

// Database Connection Function
function getDatabaseConnection() {
    $connectionString = "Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=" . DB_PATH;
    return odbc_connect($connectionString, '', '');
}

// Send Email Function
function sendEmail($to, $subject, $message) {
    $headers = "From: " . EMAIL_FROM_NAME . " <" . EMAIL_FROM . ">\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    return mail($to, $subject, $message, $headers);
}

// Security Functions
function sanitizeInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}
?>
