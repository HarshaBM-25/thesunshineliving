<!--load section-->
<?php
$section = $_GET['section'] ?? '';

switch ($section) {
    case 'profile':
        include 'user/profile.php';
        break;
    case 'bookings':
        include 'user/bookings.php';
        break;
    case 'wishlist':
        include 'user/wishlist.php';
        break;
    case 'history':
        include 'user/history.php';
        break;
    default:
        echo "Invalid section.";
        break;
}
