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
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE paystack_reference = ?");
        $stmt->execute([$reference]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $booking_code = $existing['booking_code'];
            $booking_id = $existing['id'];
        } else {
            // Generate unique code e.g. LD-1042
            $booking_code = 'LD-' . strtoupper(substr(uniqid(), -5));
            
            $insert = $pdo->prepare("INSERT INTO bookings (booking_code, client_name, client_phone, target_name, target_phone, notes, payment_status, paystack_reference) VALUES (?, ?, ?, ?, ?, ?, 'Paid', ?)");
            $insert->execute([$booking_code, $client_name, $client_phone, $target_name, $target_phone, $notes, $reference]);
            
            // Get the ID of the newly inserted row
            $booking_id = $pdo->lastInsertId();
        }

        // Calculate live queue position (pending entries up to this booking ID)
        $posStmt = $pdo->prepare("SELECT COUNT(*) AS position FROM bookings WHERE id <= ? AND status = 'Pending'");
        $posStmt->execute([$booking_id]);
        $queueData = $posStmt->fetch(PDO::FETCH_ASSOC);
        $queueNumber = $queueData['position'] ?? 1;

    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }

} else {
    $gateway_msg = $result['message'] ?? 'Unknown gateway error';
    die("Payment verification failed. Gateway response: " . htmlspecialchars($gateway_msg));
}

// Array of 10 love verses
$loveVerses = [
    ["verse" => "Love is patient, love is kind. It does not envy, it does not boast, it is not proud.", "reference" => "1 Corinthians 13:4"],
    ["verse" => "And over all these virtues put on love, which binds them all together in perfect unity.", "reference" => "Colossians 3:14"],
    ["verse" => "Many waters cannot quench love; rivers cannot sweep it away.", "reference" => "Song of Solomon 8:7"],
    ["verse" => "Let all that you do be done in love.", "reference" => "1 Corinthians 16:14"],
    ["verse" => "Dear friends, let us love one another, for love comes from God.", "reference" => "1 John 4:7"],
    ["verse" => "We love because he first loved us.", "reference" => "1 John 4:19"],
    ["verse" => "Above all, love each other deeply, because love covers a multitude of sins.", "reference" => "1 Peter 4:8"],
    ["verse" => "Anxiety weighs down the heart, but a kind word cheers it up.", "reference" => "Proverbs 12:25"],
    ["verse" => "Let love and faithfulness never leave you; bind them around your neck, write them on the tablet of your heart.", "reference" => "Proverbs 3:3"],
    ["verse" => "And now these three remain: faith, hope and love. But the greatest of these is love.", "reference" => "1 Corinthians 13:13"]
];

$randomVerse = $loveVerses[array_rand($loveVerses)];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmed - Love Diaries</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animated-verse {
            animation: fadeInUp 1s ease-out;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-slate-900 border border-slate-800 rounded-2xl p-6 text-center shadow-xl">
        <div class="w-16 h-16 bg-emerald-500/10 text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">✓</div>
        <h2 class="text-xl font-bold text-white">Payment Successful!</h2>
        <p class="text-sm text-slate-400 mt-1">Your request has been queued successfully.</p>
        
        <div class="my-6 bg-slate-950 border border-slate-800 rounded-xl p-4">
            <span class="text-xs text-slate-500 uppercase tracking-widest block">Your Unique Booking Number</span>
            <span class="text-3xl font-black text-rose-500 tracking-wider mt-1 block"><?php echo htmlspecialchars($booking_code); ?></span>
        </div>

        <!-- Live Queue Status Box -->
        <div class="mb-6 bg-slate-950 border border-blue-900/50 rounded-xl p-4">
            <span class="text-xs text-blue-400 uppercase tracking-widest block font-semibold">Your Live Queue Position</span>
            <span class="text-4xl font-black text-blue-500 mt-1 block">#<?php echo $queueNumber; ?></span>
            <span class="text-xs text-slate-500 mt-1 block">Pending requests ahead of or at your turn</span>
        </div>

        <!-- Animated Love Verse Box -->
        <div class="mb-6 bg-slate-950 border border-rose-900/50 rounded-xl p-4 animated-verse text-left">
            <span class="text-xs text-rose-400 uppercase tracking-widest block font-semibold mb-2 text-center">❤️ Daily Word on Love</span>
            <p class="text-sm text-slate-300 italic text-center">"<?php echo htmlspecialchars($randomVerse['verse']); ?>"</p>
            <p class="text-xs text-rose-400/80 text-right mt-2 font-medium">— <?php echo htmlspecialchars($randomVerse['reference']); ?></p>
        </div>

        <p class="text-xs text-slate-400">Take a screenshot of this code. Our admin team will call you via <strong>254790182919</strong> when it's your turn!</p>
        
        <div class="mt-6">
            <a href="index.php" class="text-xs text-rose-400 hover:text-rose-300 underline">Make Another Booking</a>
        </div>
    </div>
</body>
</html>
