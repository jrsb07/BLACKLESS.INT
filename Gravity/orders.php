<?php
session_start();
include "db_connect.php";

// Check if logged in and is customer
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "customer") {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Handle "Receive Order" submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['order_id']) && $_POST['action'] === 'receive') {
    $order_id = $_POST['order_id'];

    $update_sql = "UPDATE orders SET status = 'Delivered' WHERE id = ? AND user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ii", $order_id, $user_id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Order marked as delivered.'); window.location.href='orders.php';</script>";
        exit;
    } else {
        echo "<script>alert('Failed to update order.');</script>";
    }
}

// Fetch user orders
    $searchTerm = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : null;

if ($searchTerm) {
    $sql = "SELECT o.id, p.name AS product_name, o.quantity, o.total_price, o.status 
            FROM orders o 
            JOIN products p ON o.product_id = p.id 
            WHERE o.user_id = ? AND p.name LIKE ? 
            ORDER BY o.id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $searchTerm);
} else {
    $sql = "SELECT o.id, p.name AS product_name, o.quantity, o.total_price, o.status 
            FROM orders o 
            JOIN products p ON o.product_id = p.id 
            WHERE o.user_id = ? 
            ORDER BY o.id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Gravity</title>
    <link rel="stylesheet" href="orders.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <h2>Gravity</h2>
            <ul>
                <li><a href="user_dashboard.php">Dashboard</a></li>
                <li><a href="orders.php" class="active">My Orders</a></li>
                <li><a href="account.php">Account</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        
        <main class="main-content">
            <header class="topbar">
                <h2>My Orders</h2>
                <form method="GET" action="orders.php" style="display: flex; gap: 10px;">
    <input type="text" name="search" placeholder="Search Orders..." class="search-box" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
    <button type="submit" class="search-btn">Search</button>
</form>
            </header>
            
            <section class="orders">
                <h2 class="order-heading">Order History</h2>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row["id"]; ?></td>
                                <td><?php echo htmlspecialchars($row["product_name"]); ?></td>
                                <td><?php echo $row["quantity"]; ?></td>
                                <td>$<?php echo number_format($row["total_price"], 2); ?></td>
                                <td class="status <?php echo strtolower($row["status"]); ?>">
                                    <?php echo ucfirst($row["status"]); ?>

                                    <?php if (strtolower($row["status"]) === "shipped"): ?>
                                        <form method="POST" style="margin-top: 5px;">
                                            <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" name="action" value="receive">Receive Order</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>

<?php $stmt->close(); ?>
