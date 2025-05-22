<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include "db_connect.php";

// Handle actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === "deactivate") {
        $sql = "UPDATE users SET status = 'inactive' WHERE id = ?";
    } elseif ($action === "activate") {
        $sql = "UPDATE users SET status = 'active' WHERE id = ?";
    } elseif ($action === "delete") {
        $sql = "DELETE FROM users WHERE id = ?";
    }

    if (isset($sql)) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        header("Location: manage_users.php");
        exit();
    }
}

// Fetch all users
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title class="manage_users">Manage Users</title>
    <link rel="stylesheet" href="manage_users.css">
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
                <h2>Manage Users</h2>
            </header>

            <section class="manage-section">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>User Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['user_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['status'] ?? 'active'); ?></td>
                            <td>
                                <?php if (($row['status'] ?? 'active') === 'active'): ?>
                                    <a href="manage_users.php?action=deactivate&id=<?php echo $row['id']; ?>" class="btn-warning" onclick="return confirm('Deactivate this user?')">Deactivate</a>
                                <?php else: ?>
                                    <a href="manage_users.php?action=activate&id=<?php echo $row['id']; ?>" class="btn-success" onclick="return confirm('Activate this user?')">Activate</a>
                                <?php endif; ?>
                                <a href="manage_users.php?action=delete&id=<?php echo $row['id']; ?>" class="btn-danger" onclick="return confirm('Delete this user?')">Delete</a>
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
