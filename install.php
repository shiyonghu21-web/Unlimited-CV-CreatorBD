<?php
// install.php - Installation Script
header('Content-Type: text/html; charset=utf-8');

echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install AI CV Creator</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; line-height: 1.6; background: #f5f7fa; color: #333; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        header { background: #2563eb; color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        header h1 { margin-bottom: 10px; }
        .content { background: white; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .step { margin-bottom: 30px; padding: 20px; border-left: 4px solid #2563eb; background: #f8fafc; }
        .step h3 { color: #2563eb; margin-bottom: 10px; }
        .step p { margin-bottom: 15px; }
        .success { color: #059669; }
        .error { color: #dc2626; }
        .warning { color: #d97706; }
        .btn { display: inline-block; padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px; border: none; cursor: pointer; }
        .btn:hover { background: #1d4ed8; }
        pre { background: #1e293b; color: #e2e8f0; padding: 15px; border-radius: 5px; overflow-x: auto; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>AI CV Creator - Installation</h1>
            <p>Complete setup wizard for your CV creation website</p>
        </header>
        
        <div class="content">';

// Check if already installed
if (file_exists('config.php') && file_exists('database/cv_users.accdb')) {
    echo '<div class="step">
            <h3>✅ Already Installed</h3>
            <p>AI CV Creator is already installed on your server.</p>
            <p>
                <a href="index.html" class="btn">Go to Website</a>
                <a href="admin/admin.php" class="btn" style="background: #059669;">Admin Panel</a>
            </p>
        </div>';
    echo '</div></div></body></html>';
    exit;
}

// Handle installation form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<div class="step">
            <h3>🚀 Starting Installation...</h3>';
    
    // Create necessary directories
    $dirs = ['database', 'uploads', 'uploads/photos', 'api', 'admin', 'logs', 'database/backups'];
    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
            echo "<p class='success'>✅ Created directory: $dir</p>";
        }
    }
    
    // Create config.php from example
    if (!file_exists('config.php') && file_exists('config.example.php')) {
        copy('config.example.php', 'config.php');
        echo '<p class="success">✅ Created config.php from template</p>';
        echo '<p class="warning">⚠️ IMPORTANT: Edit config.php with your settings</p>';
    }
    
    // Create .gitkeep files
    file_put_contents('uploads/photos/.gitkeep', '');
    file_put_contents('database/backups/.gitkeep', '');
    file_put_contents('logs/.gitkeep', '');
    
    echo '<br><p class="success">✅ Installation completed successfully!</p>
          <p>
              <a href="index.html" class="btn">Go to Website</a>
              <a href="admin/admin.php" class="btn" style="background: #059669;">Admin Panel</a>
          </p>
          <p><strong>Important:</strong> Default admin password is <code>Admin@CV2024</code></p>
        </div>';
    
} else {
    // Show installation form
    echo '<div class="step">
            <h3>📋 System Requirements Check</h3>';
    
    $requirements = [];
    
    // Check PHP version
    $phpVersion = phpversion();
    $requirements[] = [
        'name' => 'PHP 7.4 or higher',
        'status' => version_compare($phpVersion, '7.4.0') >= 0,
        'value' => $phpVersion
    ];
    
    // Check if running on Windows
    $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    $requirements[] = [
        'name' => 'Windows Server (Recommended)',
        'status' => $isWindows,
        'value' => PHP_OS
    ];
    
    // Check file permissions
    $writablePaths = [
        'database/' => is_writable('database/') || (!file_exists('database/') && is_writable('.')),
        'uploads/' => is_writable('uploads/') || (!file_exists('uploads/') && is_writable('.'))
    ];
    
    foreach ($writablePaths as $path => $writable) {
        $requirements[] = [
            'name' => "Write permission: $path",
            'status' => $writable,
            'value' => $writable ? 'Writable' : 'Not Writable'
        ];
    }
    
    // Display requirements
    $allMet = true;
    foreach ($requirements as $req) {
        $status = $req['status'] ? '✅' : '❌';
        $class = $req['status'] ? 'success' : 'error';
        echo "<p>$status <span class='$class'>{$req['name']}</span> - {$req['value']}</p>";
        if (!$req['status']) $allMet = false;
    }
    
    if (!$allMet) {
        echo '<p class="warning">⚠️ Please fix the issues above before proceeding.</p>';
    }
    
    echo '</div>';
    
    echo '<div class="step">
            <h3>⚙️ Installation Settings</h3>
            <form method="POST" action="">
                <div style="margin: 20px 0;">
                    <label>
                        <input type="checkbox" required> I agree to install AI CV Creator
                    </label>
                </div>
                
                <p style="margin-top: 20px;">
                    <button type="submit" class="btn" ' . (!$allMet ? 'disabled' : '') . '>
                        🚀 Start Installation
                    </button>
                </p>
            </form>
        </div>';
}

echo '</div></div></body></html>';
?>
