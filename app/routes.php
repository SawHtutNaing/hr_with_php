<?php

$router->add('/', 'app/views/login.php');
$router->add('/login', 'app/views/login.php');
$router->add('/register', 'app/views/register.php');
$router->add('/logout', 'app/views/logout.php'); // Assuming you'll create a logout.php

// User routes
$router->add('/user/dashboard', 'app/views/user/dashboard.php');
$router->add('/user/profile', 'app/views/user/profile.php');
$router->add('/user/leave_request', 'app/views/user/leave_request.php');
$router->add('/user/leave_history', 'app/views/user/leave_history.php');
$router->add('/user/payroll', 'app/views/user/payroll.php');

// Admin routes
$router->add('/admin/dashboard', 'app/views/admin/dashboard.php');
$router->add('/admin/users', 'app/views/admin/users.php');
$router->add('/admin/user_edit', 'app/views/admin/user_edit.php');
$router->add('/admin/attendance', 'app/views/admin/attendance.php');
$router->add('/admin/leaves', 'app/views/admin/leaves.php');
$router->add('/admin/leave_types', 'app/views/admin/leave_types.php');
$router->add('/admin/payroll', 'app/views/admin/payroll.php');
$router->add('/admin/payroll_create', 'app/views/admin/payroll_create.php');
$router->add('/admin/bonuses', 'app/views/admin/bonuses.php');
$router->add('/admin/deductions', 'app/views/admin/deductions.php');
