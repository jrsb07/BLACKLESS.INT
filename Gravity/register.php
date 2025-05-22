<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $user_type = $_POST['user_type']; // 'customer' or 'supplier'

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    // Check if username or email already exists
    $check_user = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check_user->bind_param("ss", $username, $email);
    $check_user->execute();
    $check_user->store_result();

    if ($check_user->num_rows > 0) {
        echo "<script>alert('Username or Email already taken!'); window.history.back();</script>";
        exit();
    }
    $check_user->close();

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user/supplier data
    $stmt = $conn->prepare("INSERT INTO users (fullname, username, email, phone, password, user_type) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $fullname, $username, $email, $phone, $hashed_password, $user_type);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! Redirecting to login...'); window.location='login.html';</script>";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <div class="wrapper">
        <form action="register.php" method="POST">
            <h1>Registration</h1>

            <div class="input-box">
                <input type="text" name="fullname" placeholder="Full Name" required>
            </div>

            <div class="input-box">
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="input-box">
                <input type="text" name="phone" placeholder="Phone Number" required>
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div class="input-box">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            </div>

            <div class="input-box">
                <label>User Type:</label>
                <select name="user_type" required>
                    <option value="customer">Customer</option>
                    <option value="supplier">Supplier</option>
                </select>
            </div>

            <label><input type="checkbox" required> I declare that the information is correct.</label>

            <button type="submit" class="btn">Register</button>
        </form>
    </div>
</body>
</html>