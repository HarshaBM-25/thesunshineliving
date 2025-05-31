<?php
session_start();
require_once(__DIR__ . '/../config/database.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p style='color:red;'>You must be logged in to view profile.</p>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT name, email, phone FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<style>
.profile-card {
    background-color: #fff;
    border-radius: 15px;
    padding: 2rem;
    max-width: 500px;
    margin: 0 auto;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    color: #1a237e;
    text-align: center;
}
.profile-card h2 {
    margin-bottom: 1.2rem;
    color: #1a237e;
}
.profile-details {
    font-size: 18px;
    line-height: 1.6;
}
.profile-details span {
    font-weight: bold;
    color: #000;
}
.edit-btn, .back-btn {
    margin-top: 20px;
    background-color: #1a237e;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
}
.edit-btn:hover, .back-btn:hover {
    background-color: #3949ab;
}
</style>

<div class="profile-card">
    <h2>Your Profile</h2>
    <div class="profile-details">
        <p><span>Name:</span> <?php echo htmlspecialchars($user['name']); ?></p>
        <p><span>Email:</span> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><span>Phone:</span> <?php echo htmlspecialchars($user['phone']); ?></p>
    </div>

   <!-- <button class="edit-btn" onclick="loadSection('edit_profile')">Edit Profile</button>-->
    <br>
    <button class="back-btn" onclick="window.location.href='../user_dashboard.php'">Back to Dashboard</button>
</div>
