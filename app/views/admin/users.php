<?php



if (!is_admin()) {
    redirect('/login');
}

$db = DB::getInstance()->getConnection();

// Get all users
$stmt = $db->query("SELECT users.*, roles.name as role_name FROM users JOIN roles ON users.role_id = roles.id");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<h1 class="text-3xl font-bold mb-6 text-gray-800">User Management</h1>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="w-1/4 py-3 px-4 uppercase font-semibold text-sm text-left">Photo</th>
                    <th class="w-1/4 py-3 px-4 uppercase font-semibold text-sm text-left">Name</th>
                    <th class="w-1/4 py-3 px-4 uppercase font-semibold text-sm text-left">Email</th>
                    <th class="w-1/4 py-3 px-4 uppercase font-semibold text-sm text-left">Role</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php foreach ($users as $user): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-4">
                            <?php if ($user['photo']): ?>
                                <img src="/hrphp<?php echo $user['photo']; ?>" alt="User Photo" class="h-10 w-10 rounded-full">
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4"><?php echo $user['name']; ?></td>
                        <td class="py-3 px-4"><?php echo $user['email']; ?></td>
                        <td class="py-3 px-4"><?php echo $user['role_name']; ?></td>
                        <td class="py-3 px-4">
                            <a href="/admin/user_edit.php?id=<?php echo $user['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
