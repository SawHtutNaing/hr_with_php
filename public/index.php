<?php

// Define ROOT_PATH if not already defined (for direct access to index.php)
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . '/../');
}

require_once ROOT_PATH . 'app/init.php';

$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '';


// Basic router
$base_path = '/hrphp/public';
$request_uri = str_replace($base_path, '', $request_uri);

switch ($request_uri) {
    case '/':
    case '':
        require __DIR__ . '/../app/views/login.php';
        break;
    case '/login':
        require __DIR__ . '/../app/views/login.php';
        break;
    case '/register':
        require __DIR__ . '/../app/views/register.php';
        break;
    case '/logout':
        session_destroy();
        redirect('/login');
        break;
    case '/user/dashboard':
        if (!is_logged_in() || is_admin()) {
            redirect('/login');
        }
        require __DIR__ . '/../app/views/user/dashboard.php';
        break;
    case '/user/profile':
        if (!is_logged_in()) {
            redirect('/login');
        }
        require __DIR__ . '/../app/views/user/profile.php';
        break;
    case '/admin/dashboard':
        if (!is_admin()) {
            redirect('/login');
        }
        // require __DIR__ . '/../app/views/admin/dashboard.php';
        require '../app/views/admin/dashboard.php';
        break;
    case '/admin/bonuses':
        if (!is_admin()) {
            redirect('/login');
        }
        require __DIR__ . '/../app/views/admin/bonuses.php';
        break;
    case '/admin/deductions':
        if (!is_admin()) {
            redirect('/login');
        }
        require __DIR__ . '/../app/views/admin/deductions.php';
        break;
    case '/admin/attendance':
        if (!is_admin()) {
            redirect('/login');
        }
        require __DIR__ . '/../app/views/admin/attendance.php';
        break;
    default:
        http_response_code(404);
        require __DIR__ . '/../app/views/404.php';
        break;
}

if (isset($page_content)) {
    ob_start();
    echo $page_content;
    $content = ob_get_clean();
    require __DIR__ . '/../app/views/layout.php';
}
