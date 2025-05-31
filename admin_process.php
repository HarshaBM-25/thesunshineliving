<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT id, name, password, role FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            if ($user['role'] === 'admin') {
                // Set session and redirect to admin dashboard
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $_SESSION['error'] = "Access denied. Not an admin.";
                header("Location: admin_login.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid password.";
            header("Location: admin_login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "User not found.";
        header("Location: admin_login.php");
        exit();
    }
} else {
    header("Location: admin_login.php");
    exit();
}
