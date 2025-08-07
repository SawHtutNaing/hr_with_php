<?php


if (!is_logged_in()) {
    redirect('/login');
}

$db = DB::getInstance()->getConnection();
$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid CSRF token.');
        redirect('/user/profile');
    }

    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
    $password_new = $_POST['password'];

    try {
        // Start building the update query
        $update_fields = [];
        $params = [];

        if (!empty($name)) { $update_fields[] = "name = ?"; $params[] = $name; }
        if (!empty($email)) { $update_fields[] = "email = ?"; $params[] = $email; }
        if (!empty($phone)) { $update_fields[] = "phone = ?"; $params[] = $phone; }
        if (!empty($address)) { $update_fields[] = "address = ?"; $params[] = $address; }

        // Handle password change
        if (!empty($password_new)) {
            $hashed_password = password_hash($password_new, PASSWORD_DEFAULT);
            $update_fields[] = "password = ?";
            $params[] = $hashed_password;
        }

        // Handle file upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $target_dir = __DIR__ . '/../../public/uploads/';
            $file_name = uniqid() . '_' . basename($_FILES["photo"]["name"]);
            $target_file = $target_dir . $file_name;
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $photo_path = '/hrphp/public/uploads/' . $file_name;
                $update_fields[] = "photo = ?";
                $params[] = $photo_path;
            } else {
                set_flash_message('error', 'Failed to upload photo.');
            }
        }

        if (!empty($update_fields)) {
            $sql = "UPDATE users SET " . implode(", ", $update_fields) . " WHERE id = ?";
            $params[] = $user_id;
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            set_flash_message('success', 'Profile updated successfully!');
        } else {
            set_flash_message('info', 'No changes to save.');
        }

    } catch (PDOException $e) {
        set_flash_message('error', 'Database error: ' . $e->getMessage());
    }
    redirect('/user/profile');
}

// Fetch user data
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

ob_start();
?>

<h1 class="text-3xl font-bold mb-6 text-gray-800">My Profile</h1>

<div class="bg-white p-6 rounded-lg shadow-md">
    <?php $flash = get_flash_message(); ?>
    <?php if ($flash): ?>
        <p class="text-<?php echo $flash['type']; ?>-500 bg-<?php echo $flash['type']; ?>-100 border border-<?php echo $flash['type']; ?>-400 rounded-md px-4 py-2 mb-4"><?php echo $flash['message']; ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                Name
            </label>
            <input class="shadow-sm appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="name" name="name" type="text" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                Email
            </label>
            <input class="shadow-sm appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="email" name="email" type="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                Phone
            </label>
            <input class="shadow-sm appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="phone" name="phone" type="text" value="<?php echo htmlspecialchars($user['phone']); ?>">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="address">
                Address
            </label>
            <input class="shadow-sm appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="address" name="address" type="text" value="<?php echo htmlspecialchars($user['address']); ?>">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                New Password
            </label>
            <input class="shadow-sm appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="password" name="password" type="password">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="photo">
                Photo
            </label>
            <?php if ($user['photo']): ?>
                <img src="/hrphp<?php echo htmlspecialchars($user['photo']); ?>" alt="Current Photo" class="h-20 w-20 rounded-full mb-2 object-cover">
            <?php endif; ?>
            <input class="shadow-sm appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="photo" name="photo" type="file">
        </div>
        <div class="flex items-center justify-between">
            <button class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300" type="submit">
                Save Changes
            </button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . './../layout.php';
