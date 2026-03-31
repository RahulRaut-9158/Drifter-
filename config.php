<?php
/**
 * Central config — reads from environment variables on Railway,
 * falls back to XAMPP localhost defaults for local development.
 *
 * Railway sets these automatically when you add a MySQL plugin:
 *   MYSQLHOST, MYSQLUSER, MYSQLPASSWORD, MYSQLDATABASE, MYSQLPORT
 */

define('DB_HOST', getenv('MYSQLHOST')     ?: 'localhost');
define('DB_USER', getenv('MYSQLUSER')     ?: 'root');
define('DB_PASS', getenv('MYSQLPASSWORD') ?: '');
define('DB_PORT', getenv('MYSQLPORT')     ?: '3306');
define('DB_NAME', getenv('MYSQLDATABASE') ?: 'db');

// On Railway we use ONE database with prefixed tables.
// Locally we keep the original 3-database setup.
define('SINGLE_DB', (bool)getenv('MYSQLHOST')); // true on Railway, false locally

// Base URL — used for redirects and asset paths
// On Railway this is set as an env var: APP_URL=https://yourapp.up.railway.app
define('APP_URL', rtrim(getenv('APP_URL') ?: '', '/'));

// Base path for links — '/Drifter' locally, '' on Railway
define('BASE', getenv('APP_URL') ? '' : '/Drifter');
