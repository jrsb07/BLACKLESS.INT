<?php
session_start();
include "db_connect.php";

// Check if admin is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: login.php");
    exit();
}

// Handle approval or rejection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["withdrawal_id"])) {
    $withdrawal_id = $_POST["withdrawal_id"];
    $action = $_POST["action"];

    // Fetch withdrawal details
    $stmt = $conn->prepare("SELECT supplier_id, amount, status FROM withdrawals WHERE id = ?");
    $stmt->bind_param("i", $withdrawal_id);
    $stmt->execute();
    $withdrawal = $stmt->get_result()->fetch_assoc();

    if ($withdrawal && $withdrawal["status"] === "Pending") {
        if ($action === "approve") {
            // Deduct from supplier's balance
            $stmt = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $stmt->bind_param("di", $withdrawal['amount'], $withdrawal['supplier_id']);
            $stmt->execute();

            // Update withdrawal status and timestamp
            $stmt = $conn->prepare("UPDATE withdrawals SET status = 'Approved', processed_at = NOW() WHERE id = ?");
            $stmt->bind_param("i", $withdrawal_id);
            $stmt->execute();
        } elseif ($action === "reject") {
            $stmt = $conn->prepare("UPDATE withdrawals SET status = 'Rejected', processed_at = NOW() WHERE id = ?");
            $stmt->bind_param("i", $withdrawal_id);
            $stmt->execute();
        }
    }

    header("Location: owner_withdrawal.php");
    exit();
}

// Fetch all withdrawals including payment_details
$sql = "SELECT w.id, u.username, w.amount, w.payment_method, w.payment_details, w.status, w.requested_at, w.processed_at 
        FROM withdrawals w
        JOIN users u ON w.supplier_id = u.id
        ORDER BY w.requested_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Withdrawals</title>
    <link rel="stylesheet" href="owner_withdrawal.css">
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
                <h2>Withdrawal Requests</h2>
            </header>

            <section class="orders">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Supplier</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Payment Details</th>
                            <th>Status</th>
                            <th>Requested At</th>
                            <th>Processed At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td>$<?php echo number_format($row['amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                                    <td><?php echo htmlspecialchars($row['payment_details']); ?></td>
                                    <td><?php echo $row['status']; ?></td>
                                    <td><?php echo $row['requested_at']; ?></td>
                                    <td><?php echo $row['processed_at'] ?? 'N/A'; ?></td>
                                    <td>
                                        <?php if ($row['status'] == "Pending"): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="withdrawal_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" name="action" value="approve">Approve</button>
                                                <button type="submit" name="action" value="reject">Reject</button>
                                            </form>
                                        <?php else: ?>
                                            <em>Sucessful</em>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="9">No withdrawal requests found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
