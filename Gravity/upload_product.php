<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "supplier") {
    header("Location: login.php");
    exit();
}
include "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $price = $_POST["price"];
    $image = $_POST["image"]; // Assuming the image path is stored in DB
    $supplier_id = $_SESSION["user_id"];

    $sql = "INSERT INTO products (name, price, image, supplier_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdsi", $name, $price, $image, $supplier_id);

    if ($stmt->execute()) {
        header("Location: supplier_dashboard.php");
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Product</title>
    <link rel="stylesheet" href="upload_product.css">
</head>
<body>
    <div class="upload-container">
        <h2>Upload Product</h2>
        <form action="" method="POST">
            <label>Product Name:</label>
            <input type="text" name="name" required>

            <label>Price:</label>
            <input type="number" name="price" step="0.01" required>

            <label>Image URL:</label>
            <input type="text" name="image" required>

            <button type="submit">Upload</button>
        </form>
    </div>
</body>
</html>
