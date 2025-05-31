<?php
session_start();
$step = 'password';
$error = '';
$admin_secret = "letmein"; // Change this to your secret password

// Database config
$host = "localhost";
$username = "root";
$password = "";
$database = "blue_moon_hotel";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['secret']) && $_POST['secret'] === $admin_secret) {
        $_SESSION['admin_access_granted'] = true;
        $step = 'register';
    } elseif (isset($_POST['admin_name'], $_POST['admin_email'], $_POST['admin_phone'], $_POST['admin_password']) && isset($_SESSION['admin_access_granted']) && $_SESSION['admin_access_granted']) {
        $name = $conn->real_escape_string($_POST['admin_name']);
        $email = $conn->real_escape_string($_POST['admin_email']);
        $phone = $conn->real_escape_string($_POST['admin_phone']);
        $hashedPassword = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);

        $check = $conn->query("SELECT * FROM users WHERE email = '$email'");
        if (!$check) {
            die("Query error: " . $conn->error);
        } else {
            if ($check->num_rows > 0) {
                $error = "An account with this email already exists.";
                $step = 'register';
            } else {
                $insert = $conn->query("INSERT INTO users (name, email, password, phone, role) VALUES ('$name', '$email', '$hashedPassword', '$phone', 'admin')");
                if ($insert) {
                    $step = 'done';
                    unset($_SESSION['admin_access_granted']);
                } else {
                    $error = "Something went wrong. Please try again.";
                    $step = 'register';
                }
            }
        }
    } else {
        $error = "Incorrect secret password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Admin - The SunShine Living✨</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: #f0f4ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 450px;
            margin: 100px auto;
            background: white;
            padding: 2.5rem 3rem;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(26, 35, 126, 0.15);
        }
        .home-btn {
    display: inline-block;
    margin: 20px 0 0 20px;
    background: #1a237e;
    color: white;
    padding: 0.6rem 1rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.3s ease;
}

.home-btn:hover {
    background: #15235c;
}

        h2 {
            color: #1a237e;
            text-align: center;
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        label {
            font-weight: 600;
            display: block;
            margin-bottom: 0.5rem;
        }
        input {
            width: 100%;
            padding: 0.9rem;
            font-size: 1rem;
            border: 1.5px solid #ccc;
            border-radius: 8px;
        }
        .btn {
            width: 100%;
            padding: 1rem;
            background: #1a237e;
            color: white;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 1rem;
        }
        .btn:hover {
            background: #15235c;
        }
        .error {
            color: #dc3545;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1rem;
        }
        .success {
            color: green;
            font-weight: 600;
            text-align: center;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
<a href="index.html" class="home-btn">Home</a>
<div class="container">
    <?php if ($step === 'password'): ?>
        <h2>Enter Admin Access Password</h2>
        <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="secret">Secret Password</label>
                <input type="password" name="secret" id="secret" required />
            </div>
            <button class="btn" type="submit">Verify</button>
        </form>
    <?php elseif ($step === 'register'): ?>
        <h2>Register as Admin</h2>
        <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="admin_name">Full Name</label>
                <input type="text" name="admin_name" id="admin_name" required />
            </div>
            <div class="form-group">
                <label for="admin_email">Email</label>
                <input type="email" name="admin_email" id="admin_email" required />
            </div>
            <div class="form-group">
                <label for="admin_phone">Phone</label>
                <input type="text" name="admin_phone" id="admin_phone" />
            </div>
            <div class="form-group">
                <label for="admin_password">Password</label>
                <input type="password" name="admin_password" id="admin_password" required />
            </div>
            <button class="btn" type="submit">Register Admin</button>
        </form>
    <?php elseif ($step === 'done'): ?>
        <div class="success">Admin registered successfully! 🎉<br><br><a href="admin_login.php">Go to Admin Login</a></div>
    <?php endif; ?>
</div>
</body>
</html>
