<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "customer") {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="user_dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <h2>Gravity</h2>
            <ul>
                <li><a href="user_dashboard.php" class="active">Dashboard</a></li>
                <li><a href="orders.php">My Orders</a></li>
                <li><a href="account.php">Account</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!</h2>
                <form method="GET" action="user_dashboard.php" class="search-form">
                    <input type="text" name="search" placeholder="Search products..." class="search-box" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="search-btn">Search</button>
                </form>
            </header>

            <section class="products">
                <h2 class="avail_products">Available Products</h2>
                <div class="product-grid">
                    <?php
                    include "db_connect.php";
                    $search = isset($_GET["search"]) ? trim($_GET["search"]) : "";

                    if (!empty($search)) {
                        $stmt = $conn->prepare("SELECT * FROM products WHERE is_approved = 1 AND name LIKE ?");
                        $searchParam = "%" . $search . "%";
                        $stmt->bind_param("s", $searchParam);
                        $stmt->execute();
                        $result = $stmt->get_result();
                    } else {
                        $result = $conn->query("SELECT * FROM products WHERE is_approved = 1");
                    }

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="product">';
                            echo '<img src="' . htmlspecialchars($row["image"]) . '" alt="' . htmlspecialchars($row["name"]) . '" width="170px" height="200px">';
                            echo '<h3>' . htmlspecialchars($row["name"]) . '</h3>';
                            echo '<p>$' . number_format($row["price"], 2) . '</p>';
                            echo '<form action="place_order.php" method="POST">';
                            echo '<input type="hidden" name="product_id" value="' . $row["id"] . '">';
                            echo '<input type="hidden" name="price" value="' . $row["price"] . '">';
                            echo '<label>Quantity:</label>';
                            echo '<input type="number" name="quantity" value="1" min="1" class="qty-input">';
                            echo '<button type="submit" class="order-btn">Order Now</button>';
                            echo '</form>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No products found.</p>';
                    }
                    ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
