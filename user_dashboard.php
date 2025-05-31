<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's name
$user_sql = "SELECT name FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_data = $user_result->fetch_assoc();
$user_name = $user_data ? $user_data['name'] : "User";

// Fetch all rooms
$sql = "SELECT * FROM rooms ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>User Dashboard - Blue Moon</title>
    <link rel="stylesheet" href="css/style.css" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background: #f5f7fa;
            color: #333;
        }

        /* Header bar */
        .header-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 25px;
            z-index: 1200;
        }

        .welcome-msg {
            font-size: 18px;
            color: #1a237e;
            font-weight: 600;
        }

        .hamburger {
            font-size: 28px;
            cursor: pointer;
            color: #2c3e50;
            transition: color 0.3s ease;
        }

        .hamburger:hover {
            color: #1a237e;
        }

        /* Sidebar */
        .sidebar {
            height: 100vh;
            width: 0;
            position: fixed;
            top: 0;
            right: 0;
            background-color: #1a237e;
            overflow-x: hidden;
            transition: 0.3s;
            padding-top: 70px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .sidebar a {
            padding: 16px 30px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
            transition: background-color 0.2s ease;
            border-left: 4px solid transparent;
        }

        .sidebar a:hover {
            background-color: #3949ab;
            border-left: 4px solid #ffc107;
        }

        .sidebar .logout {
            color: #ff5252;
            margin-top: auto;
            font-weight: 600;
            border-top: 1px solid rgba(255, 255, 255, 0.3);
        }

        .closebtn {
            position: absolute;
            top: 12px;
            right: 20px;
            font-size: 32px;
            color: white;
            cursor: pointer;
        }

        /* Main Content */
        #mainContent {
            margin-top: 80px;
            padding: 0 30px 40px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        #mainContent h2 {
            text-align: center;
            margin-bottom: 2rem;
            color: #1a237e;
            font-weight: 700;
            letter-spacing: 1.2px;
        }

        .room-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.8rem;
        }

        .room-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 1.3rem;
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .room-card img {
            max-width: 100%;
            border-radius: 12px;
            height: 180px;
            object-fit: cover;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .room-card h3 {
            margin: 0.5rem 0 0.3rem;
            color: #1a237e;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .room-card p {
            margin: 0.3rem 0;
            font-size: 0.95rem;
            color: #555;
        }

        .status {
            font-weight: 600;
            margin: 0.8rem 0 1rem;
            font-size: 1rem;
        }

        .status.available { color: #4caf50; }
        .status.booked { color: #e53935; }
        .status.maintenance { color: #fb8c00; }

        .book-now-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #1a237e;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .book-now-btn:hover {
            background-color: #3949ab;
        }

        .btn-disabled {
            background-color: #9e9e9e;
            color: white;
            cursor: not-allowed;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: bold;
        }

        #sectionContent {
            padding: 20px;
            display: none;
            margin-top: 80px;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<!-- Header bar -->
<div class="header-bar">
    <div class="welcome-msg">👋 Welcome, <?php echo htmlspecialchars($user_name); ?>!</div>
    <span class="hamburger" onclick="openSidebar()">☰</span>
</div>

<!-- Sidebar -->
<div id="mySidebar" class="sidebar">
    <span class="closebtn" onclick="closeSidebar()">×</span>
    <a href="user/profile.php" onclick="loadSection('profile')">Profile</a>
    <a href="user/bookings.php" onclick="loadSection('bookings')">Bookings</a>
  <!--  <a href="#" onclick="loadSection('wishlist')">Wishlist</a>
    <a href="#" onclick="loadSection('history')">History</a>-->
    <a href="logout.php" class="logout">Logout</a>
</div>

<!-- Main Room Content -->
<div id="mainContent">
    <h2>Explore Rooms - The SunShine Living✨</h2>
    <div class="room-grid" id="roomList">
        <?php while($room = $result->fetch_assoc()): ?>
            <div class="room-card">
                <img src="<?php echo htmlspecialchars($room['image_url']); ?>" alt="Room Image" />
                <h3>Room <?php echo htmlspecialchars($room['room_number']); ?></h3>
                <p>Type: <?php echo htmlspecialchars($room['type']); ?></p>
                <p>Price: ₹<?php echo htmlspecialchars($room['price']); ?></p>
                <p class="status <?php echo $room['status']; ?>">Status: <?php echo ucfirst($room['status']); ?></p>

                <?php if ($room['status'] === 'available'): ?>
                    <a href="booking/booking_details.php?room_id=<?= $room['id'] ?>" class="book-now-btn">Book Now</a>
                <?php else: ?>
                    <button class="btn-disabled" disabled>Not Available</button>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</div>ccc

<!-- Section Content Area -->
<div id="sectionContent"></div>

<script>
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('mySidebar');
    const hamburger = document.querySelector('.hamburger');
    if (sidebar.style.width === '250px' && !sidebar.contains(event.target) && !hamburger.contains(event.target)) {
        closeSidebar();
    }
});

function openSidebar() {
    document.getElementById("mySidebar").style.width = "250px";
}

function closeSidebar() {
    document.getElementById("mySidebar").style.width = "0";
}

function loadSection(section) {
    fetch('load_section.php?section=' + section)
        .then(response => response.text())
        .then(data => {
            document.getElementById("sectionContent").innerHTML = data;
            document.getElementById("sectionContent").style.display = 'block';
            document.getElementById("mainContent").style.display = 'none';
            closeSidebar();
        })
        .catch(error => {
            document.getElementById("sectionContent").innerHTML = "<p>Error loading content.</p>";
            console.error(error);
        });
}

function showMainContent() {
    document.getElementById("mainContent").style.display = 'block';
    document.getElementById("sectionContent").style.display = 'none';
}
</script>

</body>
</html>
