<?php



if (!is_logged_in() || is_admin()) {
    redirect('/login');
}

$db = DB::getInstance()->getConnection();
$user_id = get_user_id();
$today = date('Y-m-d');

// Attendance logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid CSRF token.');
        redirect('/user/dashboard');
    }

    try {
        $stmt = $db->prepare("SELECT * FROM attendance WHERE user_id = ? AND date = ?");
        $stmt->execute([$user_id, $today]);
        $attendance_today = $stmt->fetch(PDO::FETCH_ASSOC);

        if (isset($_POST['clock_in'])) {
            if (!$attendance_today) {
                $stmt = $db->prepare("INSERT INTO attendance (user_id, clock_in, date) VALUES (?, ?, ?)");
                if ($stmt->execute([$user_id, date('H:i:s'), $today])) {
                    set_flash_message('success', 'Clocked in successfully!');
                } else {
                    set_flash_message('error', 'Failed to clock in.');
                }
            } else {
                set_flash_message('info', 'Already clocked in today.');
            }
        } elseif (isset($_POST['clock_out'])) {
            if ($attendance_today && !$attendance_today['clock_out']) {
                $stmt = $db->prepare("UPDATE attendance SET clock_out = ? WHERE id = ?");
                if ($stmt->execute([date('H:i:s'), $attendance_today['id']])) {
                    set_flash_message('success', 'Clocked out successfully!');
                } else {
                    set_flash_message('error', 'Failed to clock out.');
                }
            } else {
                set_flash_message('info', 'Not clocked in or already clocked out.');
            }
        }
    } catch (PDOException $e) {
        set_flash_message('error', 'Database error: ' . $e->getMessage());
    }
    redirect('/user/dashboard.php');
}

// Get today's attendance
$stmt = $db->prepare("SELECT * FROM attendance WHERE user_id = ? AND date = ?");
$stmt->execute([$user_id, $today]);
$attendance_today = $stmt->fetch(PDO::FETCH_ASSOC);

// Get attendance history
$stmt = $db->prepare("SELECT * FROM attendance WHERE user_id = ? ORDER BY date DESC");
$stmt->execute([$user_id]);
$attendance_history = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<h1 class="text-3xl font-bold mb-6 text-gray-800">User Dashboard</h1>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold mb-4 text-gray-700">Attendance</h2>
        <?php $flash = get_flash_message(); ?>
        <?php if ($flash): ?>
            <p class="text-<?php echo $flash['type']; ?>-500 bg-<?php echo $flash['type']; ?>-100 border border-<?php echo $flash['type']; ?>-400 rounded-md px-4 py-2 mb-4"><?php echo $flash['message']; ?></p>
        <?php endif; ?>
        <form method="POST" class="flex gap-4">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <button type="submit" name="clock_in" class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 <?php if ($attendance_today && $attendance_today['clock_in']) { echo 'opacity-50 cursor-not-allowed'; } ?>" <?php if ($attendance_today && $attendance_today['clock_in']) { echo 'disabled'; } ?>>Clock In</button>
            <button type="submit" name="clock_out" class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 <?php if (!$attendance_today || $attendance_today['clock_out']) { echo 'opacity-50 cursor-not-allowed'; } ?>" <?php if (!$attendance_today || $attendance_today['clock_out']) { echo 'disabled'; } ?>>Clock Out</button>
        </form>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold mb-4 text-gray-700">Quick Links</h2>
        <div class="flex flex-col gap-4">
            <a href="/user/leave_request.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 text-center">Request Leave</a>
            <a href="/user/payroll.php" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 text-center">View Payroll</a>
            <a href="/user/leave_history.php" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 text-center">View Leave History</a>
        </div>
    </div>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Attendance History</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Date</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Clock In</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Clock Out</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php foreach ($attendance_history as $record): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-4"><?php echo htmlspecialchars($record['date']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($record['clock_in']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($record['clock_out'] ?? 'Not clocked out yet'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
