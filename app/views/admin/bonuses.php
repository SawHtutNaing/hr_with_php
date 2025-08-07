<?php


if (!is_admin()) {
    redirect('/login');
}

$db = DB::getInstance()->getConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid CSRF token.');
        redirect('/admin/bonuses');
    }

    if (isset($_POST['add_bonus'])) {
        $user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT);
        $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $reason = filter_var($_POST['reason'], FILTER_SANITIZE_STRING);
        $date = date('Y-m-d');

        try {
            $stmt = $db->prepare("INSERT INTO bonuses (user_id, amount, reason, date) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$user_id, $amount, $reason, $date])) {
                set_flash_message('success', 'Bonus added successfully!');
            } else {
                set_flash_message('error', 'Failed to add bonus.');
            }
        } catch (PDOException $e) {
            set_flash_message('error', 'Database error: ' . $e->getMessage());
        }
    }
    redirect('/admin/bonuses');
}

// Fetch users and bonuses
$users_stmt = $db->query("SELECT id, name FROM users WHERE role_id = (SELECT id FROM roles WHERE name = 'user')");
$users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);

$bonuses_stmt = $db->query("SELECT bonuses.*, users.name as user_name FROM bonuses JOIN users ON bonuses.user_id = users.id");
$bonuses = $bonuses_stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <div>
            <h2 class="text-2xl font-semibold leading-tight">Bonuses</h2>
        </div>
        <?php $flash = get_flash_message(); ?>
        <?php if ($flash): ?>
            <p class="text-<?php echo $flash['type']; ?>-500 bg-<?php echo $flash['type']; ?>-100 border border-<?php echo $flash['type']; ?>-400 rounded-md px-4 py-2 mb-4"><?php echo $flash['message']; ?></p>
        <?php endif; ?>
        <div class="my-2 flex sm:flex-row flex-col">
            <div class="flex flex-row mb-1 sm:mb-0">
                <div class="relative">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <select name="user_id" class="appearance-none h-full rounded-l border block w-full bg-white border-gray-400 text-gray-700 py-2 px-4 pr-8 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" name="amount" placeholder="Amount" class="appearance-none rounded-r-none border border-gray-400 block w-full bg-white text-gray-700 py-2 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" step="0.01" required>
                        <input type="text" name="reason" placeholder="Reason" class="appearance-none rounded-r-none border border-gray-400 block w-full bg-white text-gray-700 py-2 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        <button type="submit" name="add_bonus" class="px-4 py-2 bg-blue-500 text-white rounded-r">Add Bonus</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Amount
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Reason
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Date
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bonuses as $bonus): ?>
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($bonus['user_name']); ?></p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($bonus['amount']); ?></p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($bonus['reason']); ?></p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($bonus['date']); ?></p>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . './../layout.php';
