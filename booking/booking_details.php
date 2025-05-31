<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;

// Fetch user details
$userQuery = $conn->prepare("SELECT name, email, phone FROM users WHERE id = ?");
$userQuery->bind_param("i", $user_id);
$userQuery->execute();
$userResult = $userQuery->get_result()->fetch_assoc();

// Fetch room details
$roomQuery = $conn->prepare("SELECT room_number, type, price FROM rooms WHERE id = ?");
$roomQuery->bind_param("i", $room_id);
$roomQuery->execute();
$room = $roomQuery->get_result()->fetch_assoc();

if (!$room) {
    echo "<p>Invalid room selected.</p>";
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $upi_id = trim($_POST['upi_id'] ?? '');
    $booking_date = $_POST['booking_date'] ?? '';
    $booking_time = $_POST['booking_time'] ?? '';

    if (!$upi_id || !$booking_date || !$booking_time) {
        $message = '<p style="color:red;">Please fill all required fields.</p>';
    } else {
        $check_in = $booking_date;
        $check_out = date('Y-m-d', strtotime($booking_date . ' +1 day'));

        $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, upi_id, check_in, check_out, status) VALUES (?, ?, ?, ?, ?, 'confirmed')");
        $stmt->bind_param("iisss", $user_id, $room_id, $upi_id, $check_in, $check_out);

        if ($stmt->execute()) {
            // Update room status to 'booked'
            $updateRoom = $conn->prepare("UPDATE rooms SET status = 'booked' WHERE id = ?");
            $updateRoom->bind_param("i", $room_id);
            $updateRoom->execute();
            $updateRoom->close();

            $message = '<p style="color:green; font-weight:bold;">Booking Successful! Thank you for choosing Blue Moon Suites.</p>';
        } else {
            $message = '<p style="color:red;">Failed to book room. Please try again.</p>';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Details - The SunShine Living✨</title>
    <style>
        /* Your existing styles here */
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #1a237e;
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-top: 12px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        input[type="time"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #1a237e;
            color: white;
            padding: 12px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 20px;
            width: 100%;
        }
        button:hover {
            background-color: #3949ab;
        }
        .room-info {
            background-color: #e8eaf6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .room-info p {
            margin: 6px 0;
        }
        .message {
            margin-top: 20px;
            text-align: center;
            font-size: 1.1em;
        }
        a button {
            width: auto;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Booking Details</h2>

    <div class="room-info">
        <p><strong>Room Number:</strong> <?php echo htmlspecialchars($room['room_number']); ?></p>
        <p><strong>Type:</strong> <?php echo htmlspecialchars($room['type']); ?></p>
        <p><strong>Price:</strong> ₹<?php echo htmlspecialchars($room['price']); ?></p>
    </div>

    <form action="" method="POST">
        <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">

        <label>Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($userResult['name']); ?>" readonly>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($userResult['email']); ?>" readonly>

        <label>Phone</label>
        <input type="tel" name="phone" value="<?php echo htmlspecialchars($userResult['phone']); ?>" readonly>

        <label>Booking Date</label>
        <input type="date" name="booking_date" required>

        <label>Booking Time</label>
        <input type="time" name="booking_time" required>

        <label>UPI ID</label>
        <input type="text" name="upi_id" placeholder="example@upi" required>

        <button type="submit">Confirm Booking</button>
    </form>

    <a href="../user_dashboard.php" style="display:block; text-align:center; margin-top:10px; text-decoration:none;">
        <button type="button" style="background-color:#4a148c; margin-top:10px;">Go to Dashboard</button>
    </a>

    <div class="message">
        <?php echo $message; ?>
    </div>
</div>

</body>
</html>
