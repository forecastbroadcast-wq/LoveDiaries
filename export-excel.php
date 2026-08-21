<?php
// export-excel.php
require_once 'db.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=love_diaries_queue.xls");

$stmt = $pdo->query("SELECT booking_code, client_name, client_phone, target_name, target_phone, notes, created_at FROM bookings ORDER BY id ASC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Booking Code\tClient Name\tClient Phone\tTarget Name\tTarget Phone\tNotes\tDate Booked\n";
foreach ($rows as $row) {
    echo "{$row['booking_code']}\t{$row['client_name']}\t{$row['client_phone']}\t{$row['target_name']}\t{$row['target_phone']}\t{$row['notes']}\t{$row['created_at']}\n";
}
exit();