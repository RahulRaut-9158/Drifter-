<?php
/**
 * Central config
 * - On Railway: reads MYSQL* environment variables set by Railway MySQL plugin
 * - On XAMPP:   falls back to localhost defaults
 */

// ── DATABASE ─────────────────────────────────────────────────────────────────
define('DB_HOST', getenv('MYSQLHOST')     ?: 'localhost');
define('DB_USER', getenv('MYSQLUSER')     ?: 'root');
define('DB_PASS', getenv('MYSQLPASSWORD') ?: '');
define('DB_PORT', getenv('MYSQLPORT')     ?: '3306');
define('DB_NAME', getenv('MYSQLDATABASE') ?: 'drifter');

// On Railway we have ONE database — courier/movers tables share it
// Locally we keep 3 separate databases
define('SINGLE_DB', (bool)getenv('MYSQLHOST'));

// ── BASE PATH ────────────────────────────────────────────────────────────────
// On Railway (no subfolder):  BASE = ''
// On XAMPP (in /Drifter/):    BASE = '/Drifter'
define('BASE', getenv('MYSQLHOST') ? '' : '/Drifter');

// ── APP URL ──────────────────────────────────────────────────────────────────
define('APP_URL', rtrim(getenv('APP_URL') ?: 'http://localhost/Drifter', '/'));
