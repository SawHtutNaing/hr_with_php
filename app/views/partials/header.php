<header class="bg-white shadow-md">
    <div class="container mx-auto px-4 py-2 flex justify-between items-center">
        <a href="/" class="text-xl font-bold">HRM</a>
        <nav>
            <ul class="flex space-x-4">
                <?php if (is_logged_in()): ?>
                    <?php if (is_admin()): ?>
                        <li><a href="/admin/dashboard.php" class="text-gray-600 hover:text-gray-800">Admin Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="/user/dashboard.php" class="text-gray-600 hover:text-gray-800">Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="/logout.php" class="text-gray-600 hover:text-gray-800">Logout</a></li>
                <?php else: ?>
                    <li><a href="/login.php" class="text-gray-600 hover:text-gray-800">Login</a></li>
                    <li><a href="/register.php" class="text-gray-600 hover:text-gray-800">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
