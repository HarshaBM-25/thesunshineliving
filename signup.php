<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'guest'; // Always guest since admin registration removed

    if ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "s", $email);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error = "Email already exists";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $phone, $hashed_password, $role);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sign Up - Blue Moon Suites</title>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <link rel="stylesheet" href="css/style.css" />
    <style>
        body {
            background: #f0f4ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color:rgb(237, 238, 246);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 1.2px;
        }

        .logo {
            font-weight: 700;
            font-size: 1.5rem;
            cursor: default;
        }

        .nav-buttons button.btn {
            background: #1a237e;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            margin-left: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s ease;
        }

        .nav-buttons button.btn:hover {
            background: #151b60;
        }

        .signup-container {
            max-width: 400px;
            margin: 120px auto;
            padding: 2rem 2.5rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .signup-container h2 {
            text-align: center;
            color: #1a237e;
            margin-bottom: 2rem;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.6rem;
            color: #333;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 1.5px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            border-color: #1a237e;
            outline: none;
            box-shadow: 0 0 6px #1a237e88;
        }

        .error {
            color: #dc3545;
            text-align: center;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .success {
            color: #28a745;
            text-align: center;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .signup-btn {
            width: 100%;
            padding: 1.1rem;
            background: #1a237e;
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background 0.3s ease;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .signup-btn:hover {
            background: #151b60;
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            font-weight: 600;
        }

        .login-link a {
            color: #1a237e;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: #151b60;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">The SunShine Living✨</div>
        <div class="nav-buttons">
            <button onclick="location.href='index.html'" class="btn">Home</button>
            <button onclick="location.href='login.php'" class="btn">Login</button>
        </div>
    </nav>

    <div class="signup-container">
        <h2>Create an Account</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required />
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required />
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" required />
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required />
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    required
                />
            </div>

            <button type="submit" class="signup-btn">Sign Up</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>
</body>
</html>
