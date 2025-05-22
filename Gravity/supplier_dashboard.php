<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "supplier") {
    header("Location: login.php");
    exit();
}
include "db_connect.php";
$supplier_id = $_SESSION["user_id"];

// Calculate total profit from delivered orders
$profit_query = "SELECT SUM(o.quantity * p.price) AS total_profit 
                 FROM orders o 
                 JOIN products p ON o.product_id = p.id 
                 WHERE p.supplier_id = ? AND o.status = 'Delivered'";
$stmt = $conn->prepare($profit_query);
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$stmt->bind_result($total_profit);
$stmt->fetch();
$stmt->close();

// Fetch supplier's current balance
$balance_query = "SELECT balance FROM users WHERE id = ?";
$stmt = $conn->prepare($balance_query);
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$stmt->bind_result($balance);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier</title>
    <link rel="stylesheet" href="supplier_dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <h2>Supplier</h2>
            <ul>
                <li><a href="supplier_dashboard.php">Dashboard</a></li>
                <li><a href="manage_products.php">Manage Products</a></li>
                <li><a href="supplier_manage_orders.php">Manage Orders</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <h2>Welcome, <?php echo $_SESSION["user_name"]; ?>!</h2>
                <div class="top-actions">
                    <p>Balance: $<?php echo number_format($balance, 2); ?></p> <!-- Display balance -->
                    <a href="upload_product.php" class="add-product-btn">+ Upload Product</a>
                    <a href="withdraw.php" class="withdraw-btn">Withdraw</a>
                </div>
            </header>

            <section class="manage-products">
                <h2>Your Approved Products</h2>
                <div class="product-grid">
                    <?php
                    $sql = "SELECT * FROM products WHERE supplier_id = ? AND is_approved = 1";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $supplier_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="product">';
                            echo '<img src="' . htmlspecialchars($row["image"]) . '" alt="' . htmlspecialchars($row["name"]) . '" width="170px" height="200px">';
                            echo '<h3>' . htmlspecialchars($row["name"]) . '</h3>';
                            echo '<p>$' . number_format($row["price"], 2) . '</p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No approved products to display.</p>';
                    }
                    ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
