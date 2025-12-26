<?php
// Prioridad: Variables de entorno > archivo .env > valores por defecto

function get_env($key, $default = null) {
    $val = getenv($key);
    if ($val !== false) return $val;
    if (isset($_ENV[$key])) return $_ENV[$key];
    return $default;
}

// Lee archivo .env solo si existe (para local)
$envFile = __DIR__ . '/../.env';
$env = [];
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
}

define('DB_HOST', get_env('DB_HOST', $env['DB_HOST'] ?? 'localhost'));
define('DB_PORT', get_env('DB_PORT', $env['DB_PORT'] ?? 3306));
define('DB_NAME', get_env('DB_NAME', $env['DB_NAME'] ?? 'libroqr'));
define('DB_USER', get_env('DB_USER', $env['DB_USER'] ?? 'root'));
define('DB_PASS', get_env('DB_PASS', $env['DB_PASS'] ?? ''));
define('DB_CHARSET', get_env('DB_CHARSET', $env['DB_CHARSET'] ?? 'utf8mb4'));

define('DB_DSN', sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
    DB_HOST,
    DB_PORT,
    DB_NAME,
    DB_CHARSET
));

define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
]);
