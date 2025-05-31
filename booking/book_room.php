<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['room_id'])) {
    header("Location: ../login.php");
    exit();
}

$room_id = $_POST['room_id'];
$user_id = $_SESSION['user_id'];

// Fetch user details
$user_sql = "SELECT name, email, phone FROM users WHERE id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

// Fetch room details
$room_sql = "SELECT * FROM rooms WHERE id = ?";
$stmt = $conn->prepare($room_sql);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$room_result = $stmt->get_result();
$room = $room_result->fetch_assoc();

// Now load the booking form page with these details
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Details - The SunShine Living✨</title>
</head>
<body>
    <h2>Booking Details</h2>
    <form action="confirm_booking.php" method="POST">
        <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">

        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
        <input type="hidden" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">

        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">

        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
        <input type="hidden" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">

        <label for="booking_date">Booking Date:</label>
        <input type="date" name="booking_date" required><br><br>

        <label for="booking_time">Booking Time:</label>
        <input type="time" name="booking_time" required><br><br>

        <label for="upi_id">Your UPI ID:</label>
        <input type="text" name="upi_id" required><br><br>

        <button type="submit">Confirm Booking</button>
    </form>
</body>
</html>
