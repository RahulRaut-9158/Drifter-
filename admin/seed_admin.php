<?php
/**
 * Drifter — Admin Account Seeder
 * Run ONCE: http://localhost/Drifter/admin/seed_admin.php
 * DELETE this file after running!
 */
require_once __DIR__ . '/../includes/db.php';
$conn = db();

$check = $conn->query("SELECT id FROM signup WHERE role='admin' LIMIT 1");
if ($check && $check->num_rows > 0) {
    die('<div style="font-family:sans-serif;padding:30px;max-width:500px;margin:40px auto;background:#f0fdf4;border:2px solid #86efac;border-radius:12px;"><h2 style="color:#166534">✅ Admin already exists!</h2><p>Delete this file.</p><a href="'.BASE.'/admin/index.php" style="display:inline-block;margin-top:12px;padding:10px 22px;background:#166534;color:white;border-radius:8px;text-decoration:none;">Go to Admin →</a></div>');
}

$username = ADMIN_USERNAME;
$email    = ADMIN_EMAIL;
$password = ADMIN_PASSWORD;
$hash     = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO signup (username, email, password, role, is_active) VALUES (?,?,?,'admin',1)");
$stmt->bind_param('sss', $username, $email, $hash);

if ($stmt->execute()) {
    echo '<div style="font-family:sans-serif;padding:30px;max-width:500px;margin:40px auto;background:#f0fdf4;border:2px solid #86efac;border-radius:12px;">';
    echo '<h2 style="color:#166534">✅ Admin Account Created!</h2>';
    echo '<p><strong>Username:</strong> ' . htmlspecialchars($username) . '</p>';
    echo '<p><strong>Password:</strong> ' . htmlspecialchars($password) . '</p>';
    echo '<p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>';
    echo '<p style="color:#dc2626;font-weight:bold;margin-top:12px;">⚠️ DELETE this file immediately!</p>';
    echo '<a href="' . BASE . '/admin/index.php" style="display:inline-block;margin-top:12px;padding:10px 22px;background:#166534;color:white;border-radius:8px;text-decoration:none;">Go to Admin Dashboard →</a>';
    echo '</div>';
} else {
    echo '<p style="font-family:sans-serif;color:red;padding:20px">❌ Error: ' . htmlspecialchars($conn->error) . '</p>';
}
$conn->close();
?>
