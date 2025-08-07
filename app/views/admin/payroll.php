<?php



if (!is_admin()) {
    redirect('/login');
}

$db = DB::getInstance()->getConnection();

// Get all payroll data
$stmt = $db->query("SELECT payroll.*, users.name as user_name FROM payroll JOIN users ON payroll.user_id = users.id ORDER BY payroll.year DESC, payroll.month DESC");
$payrolls = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<h1 class="text-3xl font-bold mb-6 text-gray-800">Payroll Management</h1>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-end mb-4">
        <a href="/admin/payroll_create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">Create Payroll</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">User</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Month/Year</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Salary</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Bonuses</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Deductions</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Net Pay</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php foreach ($payrolls as $payroll): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-4"><?php echo htmlspecialchars($payroll['user_name']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($payroll['month']) . '/' . htmlspecialchars($payroll['year']); ?></td>
                        <td class="py-3 px-4">$<?php echo number_format($payroll['salary'], 2); ?></td>
                        <td class="py-3 px-4">$<?php echo number_format($payroll['bonuses'], 2); ?></td>
                        <td class="py-3 px-4">$<?php echo number_format($payroll['deductions'], 2); ?></td>
                        <td class="py-3 px-4 font-bold">$<?php echo number_format($payroll['net_pay'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
