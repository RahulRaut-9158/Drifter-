<?php
// Alternative root structure for InfinityFree
// If subdirectories are blocked, this serves the main page directly

require_once 'config.php';
require_once 'includes/db.php';

// Simple homepage content
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drifter - Transport Services</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; text-align: center; }
        .container { max-width: 800px; margin: 0 auto; }
        .btn { background: #FF6B00; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 10px; }
        .btn:hover { background: #e55a00; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚛 Drifter Transport Services</h1>
        <p>Book transport, travel, courier, and packers & movers services across India</p>
        
        <div style="margin: 30px 0;">
            <a href="front/index.php" class="btn">Enter Main Site</a>
            <a href="front/login.php" class="btn">Login</a>
            <a href="front/signup.php" class="btn">Sign Up</a>
        </div>
        
        <div style="margin-top: 40px; color: #666;">
            <p>✅ Database Connected: <?php 
                try { 
                    $conn = db(); 
                    echo "Success"; 
                } catch(Exception $e) { 
                    echo "Failed - " . $e->getMessage(); 
                } 
            ?></p>
        </div>
    </div>
</body>
</html>