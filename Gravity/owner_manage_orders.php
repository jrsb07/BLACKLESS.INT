<?php
session_start();
include "db_connect.php";

// Check if logged in and is admin
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: login.php");
    exit();
}

// Handle confirmation (for Delivered orders)
if (isset($_POST['confirm_order'])) {
    $order_id = $_POST['order_id'];

    // Fetch the order details to get the total price, product ID, and supplier ID
    $order_sql = "SELECT total_price, product_id FROM orders WHERE id = ?";
    $stmt = $conn->prepare($order_sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order_result = $stmt->get_result()->fetch_assoc();
    $total_price = $order_result['total_price'];
    $product_id = $order_result['product_id'];

    // Fetch supplier ID from the product table
$product_sql = "SELECT supplier_id FROM products WHERE id = ?";
$stmt = $conn->prepare($product_sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_result = $stmt->get_result();
if (!$product_result) {
    die("Query failed: " . $stmt->error);
}
$product_data = $product_result->fetch_assoc();
$supplier_id = $product_data['supplier_id'];


    // Calculate 5% for the admin and 95% for the supplier
    $admin_share = $total_price * 0.05; // 5% for admin
    $supplier_share = $total_price * 0.95; // 95% for supplier

    // Update the supplier's balance (supplier receives 95%)
    $update_supplier_balance_sql = "UPDATE users SET balance = balance + ? WHERE id = ?";
   $stmt = $conn->prepare($update_supplier_balance_sql);
if (!$stmt) {
    die("Prepare failed (supplier balance): " . $conn->error);
}
$stmt->bind_param("di", $supplier_share, $supplier_id);

    $stmt->execute();

    // Update the admin's balance (admin ID = 5, admin receives 5%)
    $update_admin_balance_sql = "UPDATE users SET balance = balance + ? WHERE id = 5";  // Admin ID is 5
    $stmt = $conn->prepare($update_admin_balance_sql);
    $stmt->bind_param("d", $admin_share);
    $stmt->execute();

    // Update the order status to 'Confirmed'
    $update_sql = "UPDATE orders SET status = 'Confirmed' WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $order_id);
    
    if ($stmt->execute()) {
        // Redirect to the same page to reflect changes
        header("Location: owner_manage_orders.php");
        exit();
    } else {
        echo "Error updating order status.";
    }
}

// Fetch orders data, ordered by the latest order first
$sql = "SELECT o.id, o.user_id, o.product_id, o.quantity, o.total_price, o.status, o.order_date, u.username, p.name AS product_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN products p ON o.product_id = p.id
        ORDER BY o.order_date DESC";  // Orders by the latest date first
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="owner_manage_orders.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <h2>Admin</h2>
            <ul>
                <li><a href="owner_dashboard.php">Dashboard</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="owner_manage_products.php">Manage Products</a></li>
                <li><a href="owner_manage_orders.php">Manage Orders</a></li>
                <li><a href="owner_withdrawal.php">Withdrawals</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <h2>Manage Orders</h2>
            </header>

            <section class="orders">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Order Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['username']; ?></td>
                                    <td><?php echo $row['product_name']; ?></td>
                                    <td><?php echo $row['quantity']; ?></td>
                                    <td><?php echo $row['total_price']; ?></td>
                                    <td><?php echo $row['status']; ?></td>
                                    <td><?php echo $row['order_date']; ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'Delivered'): ?>
                                            <!-- Show Confirm Button for Delivered Orders -->
                                            <form method="POST">
                                                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" name="confirm_order">Confirm</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">No orders found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
