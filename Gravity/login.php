<?php
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, user_type FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password, $user_type);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['user_type'] = $user_type;
        $_SESSION['user_name'] = $username;

        if ($user_type === 'customer') {
            echo "<script>alert('Login successful! Redirecting to customer dashboard...'); window.location='user_dashboard.php';</script>";
        } elseif ($user_type === 'supplier') {
            echo "<script>alert('Login successful! Redirecting to supplier dashboard...'); window.location='supplier_dashboard.php';</script>";
        }elseif ($user_type === 'admin') {
        echo "<script>alert('Login successful! Redirecting to supplier dashboard...'); window.location='owner_dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid username or password!'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
