<?php

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . '/../');
}
require_once ROOT_PATH . 'app/init.php';

$db = DB::getInstance()->getConnection();

// Create roles
$roles = ['admin', 'user'];
foreach ($roles as $role) {
    $stmt = $db->prepare("INSERT OR IGNORE INTO roles (name) VALUES (?)");
    $stmt->execute([$role]);
}

// Get role IDs
$stmt = $db->prepare("SELECT id, name FROM roles");
$stmt->execute();
$roles_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$role_ids = [];
foreach ($roles_data as $role) {
    $role_ids[$role['name']] = $role['id'];
}

// Create users
$users = [
    // Admins
    ['name' => 'Admin User 1', 'email' => 'admin1@example.com', 'password' => 'password123', 'role_id' => $role_ids['admin']],
    ['name' => 'Admin User 2', 'email' => 'admin2@example.com', 'password' => 'password123', 'role_id' => $role_ids['admin']],
    ['name' => 'Admin User 3', 'email' => 'admin3@example.com', 'password' => 'password123', 'role_id' => $role_ids['admin']],

    // Employees
    ['name' => 'Employee User 1', 'email' => 'employee1@example.com', 'password' => 'password123', 'role_id' => $role_ids['user']],
    ['name' => 'Employee User 2', 'email' => 'employee2@example.com', 'password' => 'password123', 'role_id' => $role_ids['user']],
    ['name' => 'Employee User 3', 'email' => 'employee3@example.com', 'password' => 'password123', 'role_id' => $role_ids['user']],
    ['name' => 'Employee User 4', 'email' => 'employee4@example.com', 'password' => 'password123', 'role_id' => $role_ids['user']],
    ['name' => 'Employee User 5', 'email' => 'employee5@example.com', 'password' => 'password123', 'role_id' => $role_ids['user']],
    ['name' => 'Employee User 6', 'email' => 'employee6@example.com', 'password' => 'password123', 'role_id' => $role_ids['user']],
    ['name' => 'Employee User 7', 'email' => 'employee7@example.com', 'password' => 'password123', 'role_id' => $role_ids['user']],
    ['name' => 'Employee User 8', 'email' => 'employee8@example.com', 'password' => 'password123', 'role_id' => $role_ids['user']],
    ['name' => 'Employee User 9', 'email' => 'employee9@example.com', 'password' => 'password123', 'role_id' => $role_ids['user']],
    ['name' => 'Employee User 10', 'email' => 'employee10@example.com', 'password' => 'password123', 'role_id' => $role_ids['user']],
];

foreach ($users as $user) {
    $hashed_password = password_hash($user['password'], PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT OR IGNORE INTO users (name, email, password, role_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user['name'], $user['email'], $hashed_password, $user['role_id']]);
}

echo "Database seeded successfully.
";
