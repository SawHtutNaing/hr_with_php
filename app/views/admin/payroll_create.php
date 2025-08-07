<?php



if (!is_admin()) {
    redirect('/login');
}

$db = DB::getInstance()->getConnection();

// Get all users
$stmt = $db->query("SELECT id, name, salary_type, salary_rate FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$calculated_salary = 0;
$total_bonuses = 0;
$total_deductions = 0;
$net_pay = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid CSRF token.');
        redirect('/admin/payroll_create');
    }

    $user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT);
    $month = filter_var($_POST['month'], FILTER_SANITIZE_NUMBER_INT);
    $year = filter_var($_POST['year'], FILTER_SANITIZE_NUMBER_INT);

    try {
        // Fetch user salary details
        $stmt = $db->prepare("SELECT salary_type, salary_rate FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user_salary_details = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_salary_details) {
            $salary_type = $user_salary_details['salary_type'];
            $salary_rate = $user_salary_details['salary_rate'];

            // Calculate base salary
            if ($salary_type === 'hourly') {
                // Calculate total hours worked for the month
                $stmt = $db->prepare("SELECT clock_in, clock_out FROM attendance WHERE user_id = ? AND STRFTIME('%m', date) = ? AND STRFTIME('%Y', date) = ?");
                $stmt->execute([$user_id, sprintf('%02d', $month), $year]);
                $attendance_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $total_hours_worked = 0;
                foreach ($attendance_records as $record) {
                    if ($record['clock_in'] && $record['clock_out']) {
                        $in = strtotime($record['clock_in']);
                        $out = strtotime($record['clock_out']);
                        $total_hours_worked += ($out - $in) / 3600;
                    }
                }
                $calculated_salary = $total_hours_worked * $salary_rate;
            } elseif ($salary_type === 'monthly') {
                $calculated_salary = $salary_rate;
            }

            // Fetch total bonuses
            $stmt = $db->prepare("SELECT SUM(amount) FROM bonuses WHERE user_id = ? AND STRFTIME('%m', date) = ? AND STRFTIME('%Y', date) = ?");
            $stmt->execute([$user_id, sprintf('%02d', $month), $year]);
            $total_bonuses = $stmt->fetchColumn() ?? 0;

            // Fetch total deductions
            $stmt = $db->prepare("SELECT SUM(amount) FROM deductions WHERE user_id = ? AND STRFTIME('%m', date) = ? AND STRFTIME('%Y', date) = ?");
            $stmt->execute([$user_id, sprintf('%02d', $month), $year]);
            $total_deductions = $stmt->fetchColumn() ?? 0;

            $net_pay = $calculated_salary + $total_bonuses - $total_deductions;

            $stmt = $db->prepare("INSERT INTO payroll (user_id, month, year, salary, bonuses, deductions, net_pay) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$user_id, $month, $year, $calculated_salary, $total_bonuses, $total_deductions, $net_pay])) {
                set_flash_message('success', 'Payroll generated successfully!');
                redirect('/admin/payroll');
            } else {
                set_flash_message('error', 'Failed to create payroll.');
            }
        } else {
            set_flash_message('error', 'User salary details not found.');
        }
    } catch (PDOException $e) {
        set_flash_message('error', 'Database error: ' . $e->getMessage());
    }
}

ob_start();
?>

<h1 class="text-2xl font-bold mb-4">Create Payroll</h1>

<div class="bg-white p-4 rounded-md shadow-md">
    <?php $flash = get_flash_message(); ?>
    <?php if ($flash): ?>
        <p class="text-<?php echo $flash['type']; ?>-500 bg-<?php echo $flash['type']; ?>-100 border border-<?php echo $flash['type']; ?>-400 rounded-md px-4 py-2 mb-4"><?php echo $flash['message']; ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="user_id">
                User
            </label>
            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="user_id" name="user_id" required>
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name']); ?> (<?php echo ucfirst($user['salary_type']); ?>: $<?php echo htmlspecialchars($user['salary_rate']); ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="month">
                Month
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="month" name="month" type="number" min="1" max="12" value="<?php echo date('m'); ?>" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="year">
                Year
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="year" name="year" type="number" min="2000" value="<?php echo date('Y'); ?>" required>
        </div>
        <div class="flex items-center justify-between">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                Generate Payroll
            </button>
            <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="/admin/payroll.php">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
