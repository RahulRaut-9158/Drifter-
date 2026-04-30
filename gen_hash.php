<?php
// Run this ONCE locally: http://localhost/Drifter/gen_hash.php
// Copy the hash output into db_setup_infinityfree.sql then DELETE this file.
$pass = 'Admin@Drifter2025!';
echo password_hash($pass, PASSWORD_DEFAULT);
?>
