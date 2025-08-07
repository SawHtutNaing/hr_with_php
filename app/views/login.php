<?php



$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid CSRF token.');
        redirect('/login');
    }

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $db = DB::getInstance()->getConnection();

    try {
        $stmt = $db->prepare("SELECT users.*, roles.name as role_name FROM users JOIN roles ON users.role_id = roles.id WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role_name'];

            set_flash_message('success', 'Logged in successfully!');
            if ($user['role_name'] === 'admin') {
                redirect('/admin/dashboard');
            } else {
                redirect('/user/dashboard');
            }
        } else {
            $error = "Invalid credentials.";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

ob_start();
?>

<div class="flex items-center justify-center h-screen bg-gradient-to-r from-blue-500 to-purple-600">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <form method="POST">
            <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Login</h1>
            <?php $flash = get_flash_message(); ?>
            <?php if ($flash): ?>
                <p class="text-<?php echo $flash['type']; ?>-500 bg-<?php echo $flash['type']; ?>-100 border border-<?php echo $flash['type']; ?>-400 rounded-md px-4 py-2 mb-4"><?php echo $flash['message']; ?></p>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <p class="text-red-500 bg-red-100 border border-red-400 rounded-md px-4 py-2 mb-4"><?php echo $error; ?></p>
            <?php endif; ?>
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    Email
                </label>
                <input class="shadow-sm appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="email" name="email" type="email" placeholder="you@example.com" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input class="shadow-sm appearance-none border rounded w-full py-3 px-4 text-gray-700 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="password" name="password" type="password" placeholder="******************" required>
            </div>
            <div class="flex items-center justify-between">
                <button class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300" type="submit">
                    Sign In
                </button>
            </div>
            <div class="text-center mt-4">
                <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="/register">
                    Don't have an account? Sign up
                </a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
