<?php

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . '/../');
}
require_once ROOT_PATH . 'app/init.php';

$db = DB::getInstance()->getConnection();

// Create bonuses table
$db->exec("CREATE TABLE IF NOT EXISTS bonuses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    amount REAL NOT NULL,
    reason TEXT,
    date TEXT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
)");

// Create deductions table
$db->exec("CREATE TABLE IF NOT EXISTS deductions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    amount REAL NOT NULL,
    reason TEXT,
    date TEXT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
)");



echo "Database migrations completed successfully.
";
