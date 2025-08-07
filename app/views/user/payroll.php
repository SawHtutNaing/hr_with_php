<?php



if (!is_logged_in() || is_admin()) {
    redirect('/login');
}

$db = DB::getInstance()->getConnection();
$user_id = get_user_id();

// Get payroll history
$stmt = $db->prepare("SELECT * FROM payroll WHERE user_id = ? ORDER BY year DESC, month DESC");
$stmt->execute([$user_id]);
$payrolls = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<h1 class="text-3xl font-bold mb-6 text-gray-800">Payroll History</h1>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
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
