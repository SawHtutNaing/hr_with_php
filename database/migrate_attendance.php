<?php

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . '/../');
}
require_once ROOT_PATH . 'app/init.php';

$db = DB::getInstance()->getConnection();

// Create attendance table
$db->exec("CREATE TABLE IF NOT EXISTS attendance (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    clock_in TEXT,
    clock_out TEXT,
    date TEXT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
)");



echo "Database migrations for attendance and salary completed successfully.\n";

