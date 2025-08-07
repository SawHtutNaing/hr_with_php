<?php



if (!is_logged_in() || is_admin()) {
    redirect('/login');
}

$db = DB::getInstance()->getConnection();
$user_id = get_user_id();

// Get leave history
$stmt = $db->prepare("SELECT leave_requests.*, leave_types.name as leave_type_name FROM leave_requests JOIN leave_types ON leave_requests.leave_type_id = leave_types.id WHERE user_id = ? ORDER BY start_date DESC");
$stmt->execute([$user_id]);
$leave_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<h1 class="text-3xl font-bold mb-6 text-gray-800">Leave History</h1>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Leave Type</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Start Date</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">End Date</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Reason</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Status</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php foreach ($leave_requests as $request): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-4"><?php echo htmlspecialchars($request['leave_type_name']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($request['start_date']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($request['end_date']); ?></td>
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
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
