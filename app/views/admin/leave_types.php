<?php



if (!is_admin()) {
    redirect('/login');
}

$db = DB::getInstance()->getConnection();

// Add leave type
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid CSRF token.');
        redirect('/admin/leave_types');
    }

    if (isset($_POST['name'])) {
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);

        if (empty($name)) {
            set_flash_message('error', 'Leave type name cannot be empty.');
        } else {
            try {
                $stmt = $db->prepare("INSERT INTO leave_types (name) VALUES (?)");
                if ($stmt->execute([$name])) {
                    set_flash_message('success', 'Leave type added successfully!');
                } else {
                    set_flash_message('error', 'Failed to add leave type.');
                }
            } catch (PDOException $e) {
                set_flash_message('error', 'Database error: ' . $e->getMessage());
            }
        }
    }
    redirect('/admin/leave_types');
}

// Get all leave types
$stmt = $db->query("SELECT * FROM leave_types");
$leave_types = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<h1 class="text-3xl font-bold mb-6 text-gray-800">Leave Types</h1>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Add Leave Type</h2>
        <?php $flash = get_flash_message(); ?>
        <?php if ($flash): ?>
            <p class="text-<?php echo $flash['type']; ?>-500 bg-<?php echo $flash['type']; ?>-100 border border-<?php echo $flash['type']; ?>-400 rounded-md px-4 py-2 mb-4"><?php echo $flash['message']; ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                    Leave Type Name
                </label>
                <input class="shadow-sm appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" id="name" name="name" type="text" placeholder="e.g. Sick Leave" required>
            </div>
            <button class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300" type="submit">
                Add Leave Type
            </button>
        </form>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Existing Leave Types</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Name</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php foreach ($leave_types as $type): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-4"><?php echo htmlspecialchars($type['name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
