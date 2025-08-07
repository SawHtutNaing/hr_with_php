<?php

// Define ROOT_PATH if not already defined (for direct access to index.php)
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . '/../');
}

require_once ROOT_PATH . 'app/init.php';

$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/hrphp/public'; // Adjust this if your base path is different
$request_uri = str_replace($base_path, '', $request_uri);

$router = new Router();
require ROOT_PATH . 'app/routes.php';

// Handle logout separately as it involves session destruction and redirect
if ($request_uri === '/logout') {
    session_destroy();
    redirect('/login');
}

// Dispatch the request
$router->dispatch($request_uri);
