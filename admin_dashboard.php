<?php
session_start();

// Only allow access if logged in and role is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Panel - The SunShine Living✨</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f1f4f8;
        }

        .admin-header {
            padding: 0.5rem 2rem 0 2rem;
            font-size: 1rem;
            font-weight: bold;
            color:rgb(237, 238, 244);
            text-align: left;
        }

        .site-title {
            text-align: center;
            font-size: 1.8rem;
            font-weight: bold;
            color:rgb(226, 227, 238);
            padding: 0.8rem 0 0.3rem 0;
        }

        .navbar {
            background-color:rgb(250, 250, 251);
            color: white;
            padding: 0.5rem 1.5rem; /* reduced padding */
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar .logo {
            font-size: 1.3rem;
            font-weight: bold;
        }

        .dashboard-container {
            max-width: 1100px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        }

        .dashboard-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .dashboard-header h2 {
            color: #1a237e;
            font-size: 2rem;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
        }

        .card {
            background-color: #e8eaf6;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            transition: 0.3s;
            cursor: pointer;
        }

        .card:hover {
            background-color: #c5cae9;
        }

        .card i {
            font-size: 2rem;
            color: #1a237e;
            margin-bottom: 0.5rem;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1a237e;
        }

        .logout-btn {
            background-color: #d32f2f;
            color: white;
            padding: 0.6rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 2rem;
            display: inline-block;
        }

        .logout-btn:hover {
            background-color: #b71c1c;
        }
    </style>
</head>
<body>

    <!-- Admin Panel title on top-left -->
    <div class="admin-header">Admin Panel - Blue Moon Suites</div>

    <!-- Centered site title -->
    <div class="site-title">Blue Moon</div>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">Admin Panel - Blue Moon Suites🌙✨</div>
        <div class="nav-buttons">
            <form action="logout.php" method="POST" style="display: inline;">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </nav>

    <!-- Dashboard content -->
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2>Welcome, <?php echo $_SESSION['user_name']; ?>!</h2>
            <p>Manage everything from one place.</p>
        </div>

        <div class="card-grid">
            <div class="card" onclick="location.href='admin_rooms.php'">
                <i class="fas fa-bed"></i>
                <div class="card-title">View Rooms</div>
            </div>

            <div class="card" onclick="location.href='admin_bookings.php'">
                <i class="fas fa-calendar-check"></i>
                <div class="card-title">View Bookings</div>
            </div>

            <div class="card" onclick="location.href='admin_guests.php'">
                <i class="fas fa-users"></i>
                <div class="card-title">Manage Guests</div>
            </div>
        </div>
    </div>

</body>
</html>
