<?php
session_start();
include(__DIR__ . '/../config/database.php');

// Check if user is logged in
$user_id = $_SESSION['user_id'] ?? 0;

if (!$user_id) {
    echo "<p>Please log in to view your bookings.</p>";
    exit;
}

// SQL query to fetch confirmed bookings for the logged-in user
$sql = "SELECT b.*, r.room_number, r.type, r.price 
        FROM bookings b
        JOIN rooms r ON b.room_id = r.id
        WHERE b.user_id = ? AND b.status = 'confirmed'
        ORDER BY b.created_at DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("<p><strong>SQL error:</strong> " . $conn->error . "</p>");
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Bookings - The SunShine Living✨</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 25px;
            background-color: #f5f5f5;
        }
        h3 {
            color: #1a237e;
            margin-bottom: 20px;
            text-align: center;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 900px;
            margin: auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #3949ab;
            color: white;
            font-weight: 600;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        a.cancel-link {
            color: #c62828;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }
        a.cancel-link:hover {
            text-decoration: underline;
        }
        .no-bookings {
            text-align: center;
            margin-top: 40px;
            font-size: 1.2em;
            color: #555;
        }
    </style>
    <script>
        function confirmCancel() {
            return confirm("Are you sure you want to cancel this booking?");
        }
    </script>
</head>
<body>

<h3>Your Bookings</h3>

<?php if ($result->num_rows > 0): ?>
<table>
    <thead>
        <tr>
            <th>Room No</th>
            <th>Type</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Price</th>
            <th>Booked On</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                <td><?php echo htmlspecialchars(ucfirst($row['type'])); ?></td>
                <td><?php echo htmlspecialchars($row['check_in']); ?></td>
                <td><?php echo htmlspecialchars($row['check_out']); ?></td>
                <td>₹<?php echo htmlspecialchars($row['price']); ?></td>
                <td><?php echo htmlspecialchars(date('d M Y, h:i A', strtotime($row['created_at']))); ?></td>
                <td><?php echo ucfirst($row['status']); ?></td>
                <td>
                    <a href="cancel.php?booking_id=<?php echo $row['id']; ?>" 
                       class="cancel-link" 
                       onclick="return confirmCancel();">
                       Cancel
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php else: ?>
    <p class="no-bookings">You have no current bookings.</p>
<?php endif; ?>

<!-- Always show Back to Dashboard button -->
<div style="text-align: center; margin-top: 30px;">
    <a href="../user_dashboard.php" style="
        display: inline-block;
        background-color: #1a237e;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 8px;
        font-weight: bold;
    ">
        Back to Dashboard
    </a>
</div>

<?php
$stmt->close();
$conn->close();
?>


</body>
</html>
