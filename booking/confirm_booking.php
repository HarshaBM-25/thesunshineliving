<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
    $upi_id = trim($_POST['upi_id'] ?? '');
    $booking_date = $_POST['booking_date'] ?? '';
    $booking_time = $_POST['booking_time'] ?? '';

    // Simple validation
    if (!$room_id || !$upi_id || !$booking_date || !$booking_time) {
        echo "Please fill all required fields.";
        exit();
    }

    // Assuming check_in = booking_date, check_out = booking_date + 1 day (you can change logic)
    $check_in = $booking_date;
    $check_out = date('Y-m-d', strtotime($booking_date . ' +1 day'));

    // Insert booking into database
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, upi_id, check_in, check_out, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("iisss", $user_id, $room_id, $upi_id, $check_in, $check_out);

    if ($stmt->execute()) {
        // Success message
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Booking Confirmed</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f5f5f5;
                    padding: 50px;
                    text-align: center;
                }
                .message-box {
                    background: #d4edda;
                    color: #155724;
                    padding: 30px;
                    border-radius: 10px;
                    display: inline-block;
                    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                }
                a {
                    display: inline-block;
                    margin-top: 20px;
                    padding: 12px 25px;
                    background-color: #1a237e;
                    color: white;
                    text-decoration: none;
                    border-radius: 8px;
                }
                a:hover {
                    background-color: #3949ab;
                }
            </style>
        </head>
        <body>
            <div class="message-box">
                <h2>Booking Successful!</h2>
                <p>Your room has been booked successfully. Thank you for choosing The SunShine Living✨.</p>
                <a href="../userdashboard.php">Go to Dashboard</a>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "Failed to book room. Please try again.";
    }
} else {
    // Invalid request method
    header("Location: ../userdashboard.php");
    exit();
}
?>
