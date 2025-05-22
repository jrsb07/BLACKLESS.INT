<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "supplier") {
    header("Location: login.php");
    exit();
}

include "db_connect.php";

$supplier_id = $_SESSION["user_id"];

// Fetch orders for this supplier
$sql = "SELECT 
            o.id AS order_id, 
            p.name AS product_name, 
            o.quantity, 
            o.total_price, 
            o.status, 
            o.order_date 
        FROM orders o
        JOIN products p ON o.product_id = p.id
        WHERE p.supplier_id = ?
        ORDER BY o.order_date DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$result = $stmt->get_result();

// Handle status update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $update_sql = "UPDATE orders SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $status, $order_id);
    
    if ($update_stmt->execute()) {
        echo "<script>alert('Order status updated successfully'); window.location.href='supplier_manage_orders.php';</script>";
    } else {
        echo "<script>alert('Error updating order status');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="supplier_manage_orders.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <h2>Supplier</h2>
            <ul>
                <li><a href="supplier_dashboard.php">Dashboard</a></li>
                <li><a href="manage_products.php">Manage Products</a></li>
                <li><a href="supplier_manage_orders.php" class="active">Manage Orders</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <h2>Manage Orders</h2>
            </header>

            <section class="content-section">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                            <th>Admin Profit (5%)</th>
                            <th>Supplier Profit (95%)</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php
                                    $admin_profit = $row["total_price"] * 0.05;
                                    $supplier_profit = $row["total_price"] * 0.95;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row["order_id"]) ?></td>
                                    <td><?= htmlspecialchars($row["product_name"]) ?></td>
                                    <td><?= $row["quantity"] ?></td>
                                    <td>$<?= number_format($row["total_price"], 2) ?></td>
                                    <td>$<?= number_format($admin_profit, 2) ?></td>
                                    <td>$<?= number_format($supplier_profit, 2) ?></td>
                                    <td><?= date("Y-m-d", strtotime($row["order_date"])) ?></td>
                                    <td><?= htmlspecialchars($row["status"]) ?></td>
                                    <td>
                                        <?php if ($row["status"] !== "Shipped"): ?>
                                            <form action="" method="POST">
                                                <input type="hidden" name="order_id" value="<?= $row["order_id"] ?>">
                                                <select name="status">
                                                    <option value="Shipped" <?= $row["status"] === "Shipped" ? 'selected' : '' ?>>Shipped</option>
                                                </select>
                                                <button type="submit">Update</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="9" style="text-align:center;">No orders found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
