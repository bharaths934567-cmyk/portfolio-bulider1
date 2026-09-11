<?php
declare(strict_types=1);
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off']);
session_start();
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', 'portfolio_builder');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                   DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    die('The application is temporarily unavailable.');
}

function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
function csrf_field() { $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">'; }
function verify_csrf() { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(419); exit('Invalid request token.'); } }

function require_login() {
    if (empty($_SESSION['user_id'])) {
        header('Location: /auth/login.php');
        exit;
    }
}
function require_admin() {
    if (empty($_SESSION['admin_id'])) {
        header('Location: /admin/login.php');
        exit;
    }
}
