<?php
// admin/admin.php - Admin Dashboard
require_once '../config.php';
session_start();

// Check if logged in
if (!isset($_SESSION['admin_logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        if ($_POST['password'] === ADMIN_PASSWORD) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['login_time'] = time();
        } else {
            $error = "Invalid password!";
        }
    }
    
    if (!isset($_SESSION['admin_logged_in'])) {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Admin Login - AI CV Creator</title>
            <style>
                body { font-family: Arial; background: #f5f7fa; }
                .login-box { max-width: 400px; margin: 100px auto; padding: 30px; background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
                h2 { color: #2563eb; text-align: center; margin-bottom: 30px; }
                input[type="password"] { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
                button { width: 100%; padding: 12px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer; }
                .error { color: red; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class="login-box">
                <h2>Admin Login</h2>
                <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
                <form method="POST">
                    <input type="password" name="password" placeholder="Enter admin password" required>
                    <button type="submit">Login</button>
                </form>
                <p style="text-align: center; margin-top: 20px; font-size: 12px; color: #666;">Default: Admin@CV2024</p>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Check session timeout
if (time() - $_SESSION['login_time'] > SESSION_TIMEOUT) {
    session_destroy();
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - AI CV Creator</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f5f7fa; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        header { background: #2563eb; color: white; padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        header h1 { margin-bottom: 10px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-box { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        .stat-box h3 { color: #666; font-size: 14px; margin-bottom: 10px; }
        .stat-box .number { font-size: 32px; font-weight: bold; color: #2563eb; }
        .nav { background: white; padding: 15px; border-radius: 10px; margin-bottom: 30px; }
        .nav a { display: inline-block; margin-right: 15px; padding: 10px 15px; background: #f1f5f9; color: #333; text-decoration: none; border-radius: 5px; }
        .nav a:hover { background: #2563eb; color: white; }
        .logout { float: right; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>AI CV Creator - Admin Dashboard</h1>
            <p>Welcome Admin | <a href="?logout" class="logout" style="color: white;">Logout</a></p>
        </header>
        
        <div class="nav">
            <a href="#stats">Statistics</a>
            <a href="#logs">Logs</a>
            <a href="#backup">Backup</a>
            <a href="#settings">Settings</a>
        </div>
        
        <div class="stats" id="stats">
            <div class="stat-box">
                <h3>System Status</h3>
                <div class="number">Online</div>
            </div>
            <div class="stat-box">
                <h3>CVs Created</h3>
                <div class="number"><?php 
                    $logFile = '../logs/cv_creation.log';
                    if (file_exists($logFile)) {
                        $lines = file($logFile);
                        echo count($lines);
                    } else {
                        echo '0';
                    }
                ?></div>
            </div>
            <div class="stat-box">
                <h3>Photos Uploaded</h3>
                <div class="number"><?php 
                    $photoLog = '../logs/photo_uploads.log';
                    if (file_exists($photoLog)) {
                        $lines = file($photoLog);
                        echo count($lines);
                    } else {
                        echo '0';
                    }
                ?></div>
            </div>
            <div class="stat-box">
                <h3>Verifications</h3>
                <div class="number"><?php 
                    $verLog = '../logs/verifications.log';
                    if (file_exists($verLog)) {
                        $lines = file($verLog);
                        echo count($lines);
                    } else {
                        echo '0';
                    }
                ?></div>
            </div>
        </div>
        
        <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
            <h3>System Information</h3>
            <p><strong>Version:</strong> AI CV Creator 1.0</p>
            <p><strong>Admin Email:</strong> <?php echo ADMIN_EMAIL_PRIMARY; ?></p>
            <p><strong>Database:</strong> <?php echo file_exists(DB_PATH) ? 'Connected' : 'Not Found'; ?></p>
            <p><strong>Installation Path:</strong> <?php echo realpath('../'); ?></p>
        </div>
        
        <div style="background: white; padding: 20px; border-radius: 10px;">
            <h3>Quick Actions</h3>
            <div style="margin-top: 20px;">
                <a href="backup.php" style="background: #059669; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; margin-right: 10px;">
                    Backup Database
                </a>
                <a href="../logs/" target="_blank" style="background: #2563eb; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; margin-right: 10px;">
                    View Logs
                </a>
                <a href="../install.php" style="background: #d97706; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">
                    Re-run Installation
                </a>
            </div>
        </div>
    </div>
    
    <?php
    // Handle logout
    if (isset($_GET['logout'])) {
        session_destroy();
        header('Location: admin.php');
        exit;
    }
    ?>
</body>
</html>
