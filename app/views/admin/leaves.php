<?php



if (!is_admin()) {
    redirect('/login');
}

$db = DB::getInstance()->getConnection();

// Update leave request status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid CSRF token.');
        redirect('/admin/leaves');
    }

    $request_id = filter_var($_POST['request_id'], FILTER_SANITIZE_NUMBER_INT);
    $status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);

    try {
        $stmt = $db->prepare("UPDATE leave_requests SET status = ? WHERE id = ?");
        if ($stmt->execute([$status, $request_id])) {
            set_flash_message('success', 'Leave request status updated successfully!');
        } else {
            set_flash_message('error', 'Failed to update leave request status.');
        }
    } catch (PDOException $e) {
        set_flash_message('error', 'Database error: ' . $e->getMessage());
    }
    redirect('/admin/leaves');
}

// Get all leave requests
$stmt = $db->query("SELECT leave_requests.*, users.name as user_name, leave_types.name as leave_type_name FROM leave_requests JOIN users ON leave_requests.user_id = users.id JOIN leave_types ON leave_requests.leave_type_id = leave_types.id ORDER BY leave_requests.start_date DESC");
$leave_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<h1 class="text-3xl font-bold mb-6 text-gray-800">Leave Management</h1>

<div class="bg-white p-6 rounded-lg shadow-md">
    <?php $flash = get_flash_message(); ?>
    <?php if ($flash): ?>
        <p class="text-<?php echo $flash['type']; ?>-500 bg-<?php echo $flash['type']; ?>-100 border border-<?php echo $flash['type']; ?>-400 rounded-md px-4 py-2 mb-4"><?php echo $flash['message']; ?></p>
    <?php endif; ?>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">User</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Leave Type</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Dates</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Reason</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Status</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php foreach ($leave_requests as $request): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-4"><?php echo htmlspecialchars($request['user_name']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($request['leave_type_name']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($request['start_date']); ?> to <?php echo htmlspecialchars($request['end_date']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($request['reason']); ?></td>
                        <td class="py-3 px-4">
                            <span class="
                                <?php 
                                    switch ($request['status']) {
                                        case 'approved':
                                            echo 'bg-green-200 text-green-800';
                                            break;
                                        case 'rejected':
                                            echo 'bg-red-200 text-red-800';
                                            break;
                                        default:
                                            echo 'bg-yellow-200 text-yellow-800';
                                            break;
                                    }
                                ?>
                             py-1 px-3 rounded-full text-xs">
                                <?php echo ucfirst(htmlspecialchars($request['status'])); ?>
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <form method="POST" class="inline-block">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['id']); ?>">
                                <select name="status" onchange="this.form.submit()" class="rounded bg-gray-200 text-gray-700 py-1 px-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="pending" <?php echo $request['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="approved" <?php echo $request['status'] === 'approved' ? 'selected' : ''; ?>>Approve</option>
                                    <option value="rejected" <?php echo $request['status'] === 'rejected' ? 'selected' : ''; ?>>Reject</option>
                                </select>
                            </form>
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
