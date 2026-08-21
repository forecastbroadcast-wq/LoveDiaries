<?php
session_start();
require_once 'db.php'; // Your database connection

// Set your admin password here
$admin_password = "Songs"; 

$error = "";

// Handle Login
if (isset($_POST['login'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $error = "Incorrect password!";
    }
}

// Handle Logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    header("Location: admin.php");
    exit();
}

// Handle Status Update (e.g., mark as 'Called' or 'Completed')
if (isset($_POST['update_status']) && isset($_SESSION['admin_logged_in'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['status'];
    $update_stmt = $pdo->prepare("UPDATE bookings SET payment_status = ? WHERE id = ?");
    // You can also change this to a specific status column if you prefer keeping payment_status separate
    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - Love Diaries</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen p-4 md:p-8">

    <?php if (!isset($_SESSION['admin_logged_in'])): ?>
        <!-- Login Screen -->
        <div class="max-w-md mx-auto mt-20 bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <h1 class="text-xl font-bold text-rose-500 mb-4 text-center">Admin Access Required</h1>
            <?php if ($error): ?>
                <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs p-3 rounded-lg mb-4 text-center"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-rose-500">
                </div>
                <button type="submit" name="login" class="w-full bg-rose-600 hover:bg-rose-500 text-white font-bold py-2.5 rounded-xl transition duration-200">
                    Login
                </button>
            </form>
        </div>
    <?php else: ?>
        <!-- Dashboard Screen -->
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6 bg-slate-900 border border-slate-800 p-4 rounded-2xl">
                <div>
                    <h1 class="text-xl font-bold text-white">Love Diaries Queue Dashboard</h1>
                    <p class="text-xs text-slate-400 mt-0.5">Manage and call clients sequentially</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="export-excel.php" class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold px-4 py-2 rounded-xl transition">Download Excel</a>
                    <a href="admin.php?logout=true" class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold px-4 py-2 rounded-xl transition">Logout</a>
                </div>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-950/50 border-b border-slate-800 text-xs uppercase tracking-wider text-slate-400">
                                <th class="p-4">Code</th>
                                <th class="p-4">Client Name & Phone</th>
                                <th class="p-4">Target Name & Phone</th>
                                <th class="p-4">Notes</th>
                                <th class="p-4">Status</th>
                                <th class="p-4">Date Booked</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 text-sm">
                            <?php
                            $stmt = $pdo->query("SELECT * FROM bookings ORDER BY id ASC");
                            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if (empty($bookings)):
                            ?>
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-slate-500">No bookings found yet.</td>
                                </tr>
                            <?php else: foreach ($bookings as $row): ?>
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="p-4 font-mono font-bold text-rose-500"><?php echo htmlspecialchars($row['booking_code']); ?></td>
                                    <td class="p-4">
                                        <div class="font-semibold text-white"><?php echo htmlspecialchars($row['client_name']); ?></div>
                                        <a href="tel:<?php echo htmlspecialchars($row['client_phone']); ?>" class="text-xs text-rose-400 hover:underline"><?php echo htmlspecialchars($row['client_phone']); ?></a>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-semibold text-white"><?php echo htmlspecialchars($row['target_name']); ?></div>
                                        <div class="text-xs text-slate-400"><?php echo htmlspecialchars($row['target_phone']); ?></div>
                                    </td>
                                    <td class="p-4 text-xs text-slate-300 max-w-xs truncate"><?php echo htmlspecialchars($row['notes']); ?></td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            <?php echo htmlspecialchars($row['payment_status']); ?>
                                        </span>
                                    </td>
                                    <td class="p-4 text-xs text-slate-400"><?php echo htmlspecialchars($row['created_at']); ?></td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>