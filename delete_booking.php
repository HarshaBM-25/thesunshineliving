<?php
session_start();
require_once 'config/database.php';

// Check if admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);

    // First check if it's a cancelled booking
    $check = $conn->prepare("SELECT status FROM bookings WHERE id = ?");
    $check->bind_param("i", $booking_id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows > 0) {
        $status = $check_result->fetch_assoc()['status'];
        if (strtolower($status) === 'cancelled') {
            $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
            $stmt->bind_param("i", $booking_id);
            if ($stmt->execute()) {
                header("Location: admin_view_bookings.php?deleted=1");
                exit();
            } else {
                echo "Error deleting booking.";
            }
        } else {
            echo "Only cancelled bookings can be deleted.";
        }
    } else {
        echo "Booking not found.";
    }
} else {
    echo "Invalid request.";
}
?>
