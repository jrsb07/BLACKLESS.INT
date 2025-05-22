<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET fullname=?, email=?, address=?, password=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $full_name, $email, $address, $hashed_password, $user_id);
    } else {
        $query = "UPDATE users SET fullname=?, email=?, address=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $full_name, $email, $address, $user_id);
    }

    if ($stmt->execute()) {
        $_SESSION['user_name'] = $full_name;
        echo "<script>alert('Account updated successfully!'); window.location='account.php';</script>";
    } else {
        echo "<script>alert('Error updating account. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>