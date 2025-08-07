<?php



if (!is_admin()) {
    redirect('/login');
}

$db = DB::getInstance()->getConnection();

// Get all users
$stmt = $db->query("SELECT users.*, roles.name as role_name FROM users JOIN roles ON users.role_id = roles.id");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all leave requests
$stmt = $db->query("SELECT leave_requests.*, users.name as user_name, leave_types.name as leave_type_name FROM leave_requests JOIN users ON leave_requests.user_id = users.id JOIN leave_types ON leave_requests.leave_type_id = leave_types.id ORDER BY leave_requests.start_date DESC");
$leave_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<h1 class="text-3xl font-bold mb-6 text-gray-800">Admin Dashboard</h1>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold mb-2 text-gray-700">User Management</h2>
        <a href="/admin/users.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">Manage Users</a>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold mb-2 text-gray-700">Leave Management</h2>
        <a href="/admin/leaves.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">Manage Leaves</a>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold mb-2 text-gray-700">Leave Types</h2>
        <a href="/admin/leave_types.php" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">Manage Leave Types</a>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold mb-2 text-gray-700">Payroll</h2>
        <a href="/admin/payroll.php" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">Manage Payroll</a>
    </div>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Recent Leave Requests</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="w-1/4 py-3 px-4 uppercase font-semibold text-sm text-left">User</th>
                    <th class="w-1/4 py-3 px-4 uppercase font-semibold text-sm text-left">Leave Type</th>
                    <th class="w-1/4 py-3 px-4 uppercase font-semibold text-sm text-left">Start Date</th>
                    <th class="w-1/4 py-3 px-4 uppercase font-semibold text-sm text-left">End Date</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Status</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php foreach ($leave_requests as $request): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-4"><?php echo $request['user_name']; ?></td>
                        <td class="py-3 px-4"><?php echo $request['leave_type_name']; ?></td>
                        <td class="py-3 px-4"><?php echo $request['start_date']; ?></td>
                        <td class="py-3 px-4"><?php echo $request['end_date']; ?></td>
                        <td class="py-3 px-4">
                            <span class="<?php echo $request['status'] === 'approved' ? 'bg-green-200 text-green-800' : ($request['status'] === 'rejected' ? 'bg-red-200 text-red-800' : 'bg-yellow-200 text-yellow-800'); ?> py-1 px-3 rounded-full text-xs">
                                <?php echo ucfirst($request['status']); ?>
                            </span>
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
