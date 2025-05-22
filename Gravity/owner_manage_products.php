<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include "db_connect.php";

// Handle delete
if (isset($_GET["delete"])) {
    $product_id = $_GET["delete"];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    header("Location: owner_manage_products.php");
    exit();
}

// Handle approve
if (isset($_GET["approve"])) {
    $product_id = $_GET["approve"];
    $stmt = $conn->prepare("UPDATE products SET is_approved = 1 WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    header("Location: owner_manage_products.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="owner_manage_products.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <h2>Admin</h2>
            <ul>
                <li><a href="owner_dashboard.php">Dashboard</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="owner_manage_products.php" class="active">Manage Products</a></li>
                <li><a href="owner_manage_orders.php">Manage Orders</a></li>
                <li><a href="owner_withdrawal.php">Withdrawals</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <h2>Manage Products</h2>
            </header>

            <section class="content-section">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Supplier</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT p.*, u.username AS supplier_name 
                                FROM products p 
                                LEFT JOIN users u ON p.supplier_id = u.id";
                        $result = $conn->query($sql);

                        if (!$result) {
                            die("Query failed: " . $conn->error);
                        }

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>{$row['id']}</td>";
                                echo "<td>".htmlspecialchars($row['supplier_name'] ?? 'Unknown')."</td>";
                                echo "<td>".htmlspecialchars($row['name'])."</td>";
                                echo "<td>$".number_format($row['price'], 2)."</td>";
                                echo "<td><img src='".htmlspecialchars($row['image'])."' width='80' height='80' style='object-fit:cover;'></td>";
                                
                                // Show Approved or Pending
                                echo "<td>";
                                if ($row['is_approved'] == 1) {
                                    echo "<span style='color:green;'>Approved</span>";
                                } else {
                                    echo "<span style='color:orange;'>Pending</span>";
                                }
                                echo "</td>";

                                // Actions: Approve + Delete
                                echo "<td>";
                                if ($row['is_approved'] == 0) {
                                    echo "<a href='owner_manage_products.php?approve={$row['id']}' class='action-btn approve' onclick=\"return confirm('Approve this product?');\">Approve</a> ";
                                }
                                echo "<a href='owner_manage_products.php?delete={$row['id']}' class='action-btn delete' onclick=\"return confirm('Are you sure you want to delete this product?');\">Delete</a>";
                                echo "</td>";

                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' style='text-align:center;'>No products found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
