<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "supplier") {
    header("Location: login.php");
    exit();
}

include "db_connect.php";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $id = $_POST["id"];
    $supplier_id = $_SESSION["user_id"];

    // Ensure the product belongs to the supplier
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND supplier_id = ?");
    $stmt->bind_param("ii", $id, $supplier_id);
    $stmt->execute();
}

header("Location: manage_products.php");
exit();
?>