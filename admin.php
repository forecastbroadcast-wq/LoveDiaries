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

// Handle Booking Deletion (Remove completed/handled client)
if (isset($_POST['delete_booking']) && isset($_SESSION['admin_logged_in'])) {
    $booking_id = $_POST['booking_id'];
    $del_stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
    $del_stmt->execute([$booking_id]);
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
<body class="bg-slate-950 text-slate-100 min-h-screen p-3 md:p-8">

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
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 bg-slate-900 border border-slate-800 p-4 rounded-2xl">
                <div>
                    <h1 class="text-lg md:text-xl font-bold text-white">Love Diaries Queue Dashboard</h1>
                    <p class="text-xs text-slate-400 mt-0.5">Manage and call clients sequentially</p>
                </div>
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <a href="export-excel.php" class="flex-1 md:flex-none text-center bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold px-4 py-2 rounded-xl transition">Download Excel</a>
                    <a href="admin.php?logout=true" class="flex-1 md:flex-none text-center bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold px-4 py-2 rounded-xl transition">Logout</a>
                </div>
            </div>

            <?php
            $stmt = $pdo->query("SELECT * FROM bookings ORDER BY id ASC");
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($bookings)):
            ?>
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8 text-center text-slate-500 shadow-xl">
                    No bookings found yet.
                </div>
            <?php else: ?>

                <!-- DESKTOP TABLE VIEW (Hidden on mobile) -->
                <div class="hidden md:block bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
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
                                    <th class="p-4 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800 text-sm">
                                <?php foreach ($bookings as $row): ?>
                                    <tr class="hover:bg-slate-800/40 transition">
                                        <td class="p-4 font-mono font-bold text-rose-500"><?php echo htmlspecialchars($row['booking_code'] ?? ''); ?></td>
                                        <td class="p-4">
                                            <div class="font-semibold text-white"><?php echo htmlspecialchars($row['client_name'] ?? ''); ?></div>
                                            <a href="tel:<?php echo htmlspecialchars($row['client_phone'] ?? ''); ?>" class="text-xs text-rose-400 hover:underline"><?php echo htmlspecialchars($row['client_phone'] ?? ''); ?></a>
                                        </td>
                                        <td class="p-4">
                                            <div class="font-semibold text-white"><?php echo htmlspecialchars($row['target_name'] ?? ''); ?></div>
                                            <div class="text-xs text-slate-400"><?php echo htmlspecialchars($row['target_phone'] ?? ''); ?></div>
                                        </td>
                                        <td class="p-4 text-xs text-slate-300 max-w-xs truncate"><?php echo htmlspecialchars($row['notes'] ?? ''); ?></td>
                                        <td class="p-4">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                <?php echo htmlspecialchars($row['payment_status'] ?? ''); ?>
                                            </span>
                                        </td>
                                        <td class="p-4 text-xs text-slate-400"><?php echo htmlspecialchars($row['date_booked'] ?? ''); ?></td>
                                        <td class="p-4 text-center">
                                            <form method="POST" onsubmit="return confirm('Remove this client from the queue?');">
                                                <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" name="delete_booking" class="bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/20 text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- MOBILE CARD VIEW (Hidden on desktop) -->
                <div class="md:hidden space-y-4">
                    <?php foreach ($bookings as $row): ?>
                        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-xl space-y-3">
                            <div class="flex justify-between items-center border-b border-slate-800 pb-2">
                                <span class="font-mono font-bold text-rose-500 text-base"><?php echo htmlspecialchars($row['booking_code'] ?? ''); ?></span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    <?php echo htmlspecialchars($row['payment_status'] ?? ''); ?>
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-slate-500 uppercase tracking-wider block">Client</span>
                                    <span class="font-semibold text-white block"><?php echo htmlspecialchars($row['client_name'] ?? ''); ?></span>
                                    <a href="tel:<?php echo htmlspecialchars($row['client_phone'] ?? ''); ?>" class="text-rose-400 underline"><?php echo htmlspecialchars($row['client_phone'] ?? ''); ?></a>
                                </div>
                                <div>
                                    <span class="text-slate-500 uppercase tracking-wider block">Target</span>
                                    <span class="font-semibold text-white block"><?php echo htmlspecialchars($row['target_name'] ?? ''); ?></span>
                                    <span class="text-slate-400 block"><?php echo htmlspecialchars($row['target_phone'] ?? ''); ?></span>
                                </div>
                            </div>

                            <?php if (!empty($row['notes'])): ?>
                                <div class="bg-slate-950 p-2.5 rounded-xl border border-slate-800/80 text-xs">
                                    <span class="text-slate-500 uppercase tracking-wider block mb-0.5">Notes</span>
                                    <p class="text-slate-300"><?php echo htmlspecialchars($row['notes']); ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="flex justify-between items-center pt-2 border-t border-slate-800/80 text-xs text-slate-400">
                                <span><?php echo htmlspecialchars($row['date_booked'] ?? ''); ?></span>
                                <form method="POST" onsubmit="return confirm('Remove this client from the queue?');">
                                    <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete_booking" class="bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/20 text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>
        </div>
    <?php endif; ?>
</body>
</html>
