<?php
// success.php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db.php'; // Your database connection file

$reference = $_GET['reference'] ?? '';

if (!$reference) {
    die("Invalid access. No reference supplied.");
}

// Verify transaction via Paystack API
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_HTTPHEADER => [
        "accept: application/json",
        "authorization: Bearer sk_test_815a0df4177b0eaf40c82453653c9dd92eb8888b"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    die("Curl Error: " . $err);
}

$result = json_decode($response, true);
$payment_status = $result['data']['status'] ?? '';

if ($result && ($payment_status === 'success' || (isset($result['status']) && $result['status'] === true && $payment_status === 'success'))) {
    $meta = $result['data']['metadata']['custom_fields'] ?? [];
    
    // Extract fields from metadata safely
    $client_name = ''; $client_phone = ''; $target_name = ''; $target_phone = ''; $notes = '';
    foreach($meta as $field) {
        if($field['variable_name'] == 'client_name') $client_name = $field['value'];
        if($field['variable_name'] == 'client_phone') $client_phone = $field['value'];
        if($field['variable_name'] == 'target_name') $target_name = $field['value'];
        if($field['variable_name'] == 'target_phone') $target_phone = $field['value'];
        if($field['variable_name'] == 'notes') $notes = $field['value'];
    }

    try {
        // Ensure PDO throws exceptions on error
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if booking already exists for this reference
        $stmt = $pdo->prepare("SELECT booking_code FROM bookings WHERE paystack_reference = ?");
        $stmt->execute([$reference]);
        $existing = $stmt->fetch();

        if ($existing) {
            $booking_code = $existing['booking_code'];
        } else {
            // Generate unique code e.g. LD-1042
            $booking_code = 'LD-' . strtoupper(substr(uniqid(), -5));
            
            $insert = $pdo->prepare("INSERT INTO bookings (booking_code, client_name, client_phone, target_name, target_phone, notes, payment_status, paystack_reference) VALUES (?, ?, ?, ?, ?, ?, 'Paid', ?)");
            $insert->execute([$booking_code, $client_name, $client_phone, $target_name, $target_phone, $notes, $reference]);
        }
    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }

} else {
    $gateway_msg = $result['message'] ?? 'Unknown gateway error';
    die("Payment verification failed. Gateway response: " . htmlspecialchars($gateway_msg));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmed - Love Diaries</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-slate-900 border border-slate-800 rounded-2xl p-6 text-center shadow-xl">
        <div class="w-16 h-16 bg-emerald-500/10 text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">✓</div>
        <h2 class="text-xl font-bold text-white">Payment Successful!</h2>
        <p class="text-sm text-slate-400 mt-1">Your test request has been queued successfully.</p>
        
        <div class="my-6 bg-slate-950 border border-slate-800 rounded-xl p-4">
            <span class="text-xs text-slate-500 uppercase tracking-widest block">Your Unique Booking Number</span>
            <span class="text-3xl font-black text-rose-500 tracking-wider mt-1 block"><?php echo htmlspecialchars($booking_code); ?></span>
        </div>

        <p class="text-xs text-slate-400">Take a screenshot of this code. Our admin team will call you via <strong>254790182919</strong> when it's your turn!</p>
    </div>
</body>
</html>