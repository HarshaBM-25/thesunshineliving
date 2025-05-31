<?php
session_start();
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Login - Blue Moon Suites🌙✨</title>
<link rel="stylesheet" href="css/style.css" />
<style>
    /* Container and card styling matching typical signup style */
    body {
        background: #f0f4ff;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
    }
    nav.navbar {
        background-color: #1a237e;
        color: white;
        padding: 1rem 2rem;
        display: flex;
        align-items: center;
        font-weight: 700;
        font-size: 1.5rem;
        letter-spacing: 1px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
    nav .logo {
        flex-grow: 1;
        cursor: pointer;
        color : #fff;
    }
    nav .nav-buttons button {
        background-color: transparent;
        border: 2px solid white;
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    nav .nav-buttons button:hover {
        background-color: white;
        color: #1a237e;
    }
    .login-container {
        max-width: 400px;
        margin: 120px auto;
        background: white;
        padding: 2.5rem 3rem;
        border-radius: 12px;
        box-shadow: 0 6px 18px rgba(26, 35, 126, 0.15);
    }
    h2 {
        color: #1a237e;
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 2rem;
        text-align: center;
        letter-spacing: 1px;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #333;
    }
    input[type="email"], input[type="password"] {
        width: 100%;
        padding: 0.9rem 1.2rem;
        font-size: 1rem;
        border: 1.5px solid #ccc;
        border-radius: 8px;
        transition: border-color 0.3s ease;
        font-family: inherit;
    }
    input[type="email"]:focus, input[type="password"]:focus {
        border-color: #1a237e;
        outline: none;
    }
    .error {
        color: #dc3545;
        font-weight: 600;
        margin-bottom: 1rem;
        text-align: center;
    }
    .admin-login-btn {
        width: 100%;
        background-color: #1a237e;
        color: white;
        font-weight: 700;
        font-size: 1.1rem;
        padding: 1rem 0;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        letter-spacing: 0.5px;
    }
    .admin-login-btn:hover {
        background-color: #15235c;
    }
</style>
</head>
<body>

<nav class="navbar">
    <div class="logo" onclick="location.href='index.html'">The SunShine Living✨</div>
    <div class="nav-buttons">
    <button onclick="location.href='index.html'">Home</button>
    <button onclick="location.href='admin_reg.php'">Admin Register</button>
</div>

</nav>

<div class="login-container">
    <h2>Admin Login</h2>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="admin_process.php" novalidate>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autocomplete="username" />
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password" />
        </div>

        <button type="submit" class="admin-login-btn">Login as Admin</button>
    </form>
</div>

</body>
</html>
