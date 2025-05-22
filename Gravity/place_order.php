<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "customer") {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"];
    $product_id = $_POST["product_id"];
    $price = $_POST["price"];
    $quantity = $_POST["quantity"];
    $total_price = $price * $quantity;
    $status = "Pending";

    $query = "INSERT INTO orders (user_id, product_id, quantity, total_price, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiids", $user_id, $product_id, $quantity, $total_price, $status);

    if ($stmt->execute()) {
        header("Location: orders.php");
        exit();
    } else {
        echo "Error placing order: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>

