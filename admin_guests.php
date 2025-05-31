<?php
session_start();
require_once 'config/database.php';

// Check admin session
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Fetch all guests (users with role = 'guest')
$sql = "SELECT id, name, email, phone, created_at FROM users WHERE role = 'guest' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin - Manage Guests</title>
    <link rel="stylesheet" href="css/style.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 2rem;
        }
        h2 {
            color: #1a237e;
            text-align: center;
            margin-bottom: 2rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #1a237e;
            color: white;
        }
        tr:hover {
            background: #f1f1f1;
        }
    </style>
</head>
<body>
<form action="admin_dashboard.php" method="get" style="margin-bottom: 1rem;">
    <button style="background-color: #1a237e; color: white; padding: 0.5rem 1rem; border: none; border-radius: 5px; cursor: pointer;">← Back to Dashboard</button>
</form>


<h2>Guest Management - The SunShine Living✨</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Registered On</th>
    </tr>

    <?php if ($result->num_rows > 0): ?>
        <?php while($guest = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($guest['id']); ?></td>
            <td><?php echo htmlspecialchars($guest['name']); ?></td>
            <td><?php echo htmlspecialchars($guest['email']); ?></td>
            <td><?php echo htmlspecialchars($guest['phone'] ?? 'N/A'); ?></td>
            <td><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($guest['created_at']))); ?></td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="5" style="text-align:center;">No guests found.</td>
        </tr>
    <?php endif; ?>
</table>

</body>
</html>
