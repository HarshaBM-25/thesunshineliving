<?php
session_start();
include(__DIR__ . '/../config/database.php');

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
    die("Please log in first.");
}

$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
if (!$booking_id) {
    die("Invalid booking ID.");
}

// Fetch booking info to confirm it belongs to this user and is confirmed
$stmt = $conn->prepare("SELECT b.*, r.room_number FROM bookings b JOIN rooms r ON b.room_id = r.id WHERE b.id = ? AND b.user_id = ? AND b.status = 'confirmed'");
if (!$stmt) die("DB error: " . $conn->error);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Booking not found or cannot be cancelled.");
}
$booking = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reason = trim($_POST['cancel_reason'] ?? '');
    if (empty($reason)) {
        $error = "Please provide a reason for cancellation.";
    } else {
        // Update booking status + store cancellation reason
        $updateBooking = $conn->prepare("UPDATE bookings SET status = 'cancelled', cancel_reason = ? WHERE id = ?");
        if (!$updateBooking) die("Prepare error: " . $conn->error);
        $updateBooking->bind_param("si", $reason, $booking_id);
        $updateBooking->execute();

        // Update room status to available
        $updateRoom = $conn->prepare("UPDATE rooms SET status = 'available' WHERE id = ?");
        if (!$updateRoom) die("Prepare error: " . $conn->error);
        $updateRoom->bind_param("i", $booking['room_id']);
        $updateRoom->execute();

        echo "<p style='color:green;'>Booking cancelled successfully.</p>";
        echo "<p><a href='bookings.php'>Back to your bookings</a></p>";
        exit;
    }
}
?>

<h3>Cancel Booking for Room <?php echo htmlspecialchars($booking['room_number']); ?></h3>

<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST" action="">
    <label for="cancel_reason">Reason for cancellation:</label><br>
    <textarea name="cancel_reason" id="cancel_reason" rows="4" cols="50" required><?php echo htmlspecialchars($_POST['cancel_reason'] ?? ''); ?></textarea><br><br>
    <button type="submit" style="background:#cc0000; color:#fff; padding:8px 15px; border:none; cursor:pointer;">Confirm Cancel</button>
    &nbsp; <a href="bookings.php">Back to Bookings</a>
</form>
