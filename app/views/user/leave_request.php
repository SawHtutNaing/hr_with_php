<?php



if (!is_logged_in() || is_admin()) {
    redirect('/login');
}

$db = DB::getInstance()->getConnection();
$user_id = get_user_id();

// Get leave types
$stmt = $db->query("SELECT * FROM leave_types");
$leave_types = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid CSRF token.');
        redirect('/user/leave_request');
    }

    $leave_type_id = filter_var($_POST['leave_type_id'], FILTER_SANITIZE_NUMBER_INT);
    $start_date = filter_var($_POST['start_date'], FILTER_SANITIZE_STRING);
    $end_date = filter_var($_POST['end_date'], FILTER_SANITIZE_STRING);
    $reason = filter_var($_POST['reason'], FILTER_SANITIZE_STRING);

    if (empty($leave_type_id) || empty($start_date) || empty($end_date)) {
        set_flash_message('error', 'Please fill in all required fields.');
    } elseif (strtotime($start_date) > strtotime($end_date)) {
        set_flash_message('error', 'Start date cannot be after end date.');
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO leave_requests (user_id, leave_type_id, start_date, end_date, reason) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$user_id, $leave_type_id, $start_date, $end_date, $reason])) {
                set_flash_message('success', 'Leave request submitted successfully!');
                redirect('/user/dashboard');
            } else {
                set_flash_message('error', 'Failed to submit leave request.');
            }
        } catch (PDOException $e) {
            set_flash_message('error', 'Database error: ' . $e->getMessage());
        }
    }
    redirect('/user/leave_request');
}

ob_start();
?>

<h1 class="text-3xl font-bold mb-6 text-gray-800">Leave Request</h1>

<div class="bg-white p-6 rounded-lg shadow-md">
    <?php $flash = get_flash_message(); ?>
    <?php if ($flash): ?>
        <p class="text-<?php echo $flash['type']; ?>-500 bg-<?php echo $flash['type']; ?>-100 border border-<?php echo $flash['type']; ?>-400 rounded-md px-4 py-2 mb-4"><?php echo $flash['message']; ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="leave_type_id">
                Leave Type
            </label>
            <select class="shadow-sm appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" id="leave_type_id" name="leave_type_id" required>
                <?php foreach ($leave_types as $type): ?>
                    <option value="<?php echo htmlspecialchars($type['id']); ?>"><?php echo htmlspecialchars($type['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="start_date">
                    Start Date
                </label>
                <input class="shadow-sm appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" id="start_date" name="start_date" type="date" required>
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="end_date">
                    End Date
                </label>
                <input class="shadow-sm appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" id="end_date" name="end_date" type="date" required>
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="reason">
                Reason
            </label>
            <textarea class="shadow-sm appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" id="reason" name="reason" rows="4" placeholder="Please provide a reason for your leave"></textarea>
        </div>
        <div class="flex items-center justify-end gap-4">
            <a href="/user/dashboard.php" class="text-gray-600 hover:text-gray-800 font-bold">Cancel</a>
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg focus:outline-none focus:shadow-outline transition duration-300" type="submit">
                Submit Request
            </button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
