<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "supplier") {
    header("Location: login.php");
    exit();
}
include "db_connect.php";

$supplier_id = $_SESSION["user_id"];

// Fetch supplier's products
$sql = "SELECT * FROM products WHERE supplier_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="manage_products.css">
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
                <h2>Your Products</h2>
                <a href="upload_product.php" class="add-product-btn">+ Upload Product</a>
            </header>

            <section class="manage-products">
                <div class="product-grid">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="product">
                            <img src="<?php echo htmlspecialchars($row["image"]); ?>" alt="<?php echo htmlspecialchars($row["name"]); ?>" width="170px" height="200px">
                            <h3><?php echo htmlspecialchars($row["name"]); ?></h3>
                            <p>$<?php echo number_format($row["price"], 2); ?></p>
                            <form action="edit_product.php" method="GET" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
                                <button type="submit" class="edit-btn">Edit</button>
                            </form>
                            <form action="delete_product.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>