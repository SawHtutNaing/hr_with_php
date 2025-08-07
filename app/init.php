<?php

session_start();



// Database connection
require_once __DIR__ . '/db.php';

// Configure PDO to throw exceptions
DB::getInstance()->getConnection()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Functions
require_once __DIR__ . '/functions.php';

// Helpers
require_once __DIR__ . '/helpers.php';

// Config
require_once __DIR__ . '/config.php';

// Autoload classes
spl_autoload_register(function ($class) {
    if (file_exists(__DIR__ . '/classes/' . $class . '.php')) {
        require_once __DIR__ . '/classes/' . $class . '.php';
    }
});

function redirect($url) {
    header('Location: ' . $url);
    exit();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function get_user_role() {
    return $_SESSION['role'] ?? null;
}

// CSRF Protection Functions
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        // Token is invalid or missing
        return false;
    }
    return true;
}

// Flash Message Functions
function set_flash_message($type, $message) {
    $_SESSION['flash_message'] = ['type' => $type, 'message' => $message];
}

function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}
