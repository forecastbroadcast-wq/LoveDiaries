<?php
// Retrieve database credentials from Render environment variables
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$password = getenv('DB_PASS');

try {
    // Connect using PDO for PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Automatically create the bookings table if it doesn't exist yet
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS bookings (
            id SERIAL PRIMARY KEY,
            booking_code VARCHAR(50) NOT NULL,
            client_name VARCHAR(100) NOT NULL,
            client_phone VARCHAR(50) NOT NULL,
            target_name VARCHAR(100) NOT NULL,
            target_phone VARCHAR(50) NOT NULL,
            notes TEXT,
            status VARCHAR(20) DEFAULT 'Pending',
            payment_status VARCHAR(50) DEFAULT 'Pending',
            paystack_reference VARCHAR(100),
            date_booked TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Fallback in case the table already existed without the column
    $pdo->exec("
        ALTER TABLE bookings ADD COLUMN IF NOT EXISTS payment_status VARCHAR(50) DEFAULT 'Pending';
    ");

} catch (PDOException $e) {
    die("Database Connection & Setup Error: " . $e->getMessage());
}
?>
