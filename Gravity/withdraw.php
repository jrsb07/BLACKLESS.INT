<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "supplier") {
    header("Location: login.php");
    exit();
}

include "db_connect.php";
$supplier_id = $_SESSION["user_id"];

// Fetch current balance
$query = "SELECT balance FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$stmt->bind_result($available_balance);
$stmt->fetch();
$stmt->close();

$message = "";

// Handle withdrawal request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $withdraw_amount = floatval($_POST["amount"]);
    $payment_method = trim($_POST["payment_method"]);
    $payment_details = trim($_POST["payment_details"] ?? "");

    if ($withdraw_amount <= 0) {
        $message = "Invalid withdrawal amount.";
    } elseif ($withdraw_amount > $available_balance) {
        $message = "You cannot withdraw more than your available balance.";
    } elseif (empty($payment_method)) {
        $message = "Please select a payment method.";
    } elseif (empty($payment_details)) {
        $message = "Please enter payment details.";
    } else {
        $insert_sql = "INSERT INTO withdrawals (supplier_id, amount, payment_method, payment_details) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        if ($stmt) {
            $stmt->bind_param("idss", $supplier_id, $withdraw_amount, $payment_method, $payment_details);
            if ($stmt->execute()) {
                $message = "Withdrawal request of $" . number_format($withdraw_amount, 2) . " via $payment_method submitted successfully.";
            } else {
                $message = "Error submitting request: " . $stmt->error;
            }
        } else {
            $message = "Prepare failed: " . $conn->error;
        }
    }
}

// Fetch all withdrawal requests
$withdrawals_sql = "SELECT amount, payment_method, payment_details, status, requested_at, processed_at FROM withdrawals WHERE supplier_id = ? ORDER BY requested_at DESC";
$stmt = $conn->prepare($withdrawals_sql);
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Withdraw Funds</title>
    <link rel="stylesheet" href="supplier_dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <main class="main-content">
            <header class="topbar">
                <h2>Withdraw Funds</h2>
                <a href="supplier_dashboard.php" class="back-button">← Back to Dashboard</a>
            </header>

            <section class="withdraw-section">
                <p><strong>Available Balance:</strong> $<?php echo number_format($available_balance, 2); ?></p>

                <form method="POST" action="">
                    <label for="amount">Amount to Withdraw:</label>
                    <input type="number" step="0.01" name="amount" id="amount" required>

                    <label for="payment_method">Payment Method:</label>
                    <select name="payment_method" id="payment_method" required>
                        <option value="">-- Select Method --</option>
                        <option value="GCash">GCash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="PayPal">PayPal</option>
                        <option value="Maya">Maya</option>
                    </select>

                    <!-- Dynamic Field -->
                    <label for="payment_details" id="payment_details_label" style="display:none;">Enter Payment Details:</label>
                    <input type="text" name="payment_details" id="payment_details" style="display:none;" required>

                    <button type="submit">Submit Request</button>
                </form>

                <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>
            </section>

            <section class="withdraw-history">
                <h3>Your Withdrawal Requests</h3>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Details</th>
                            <th>Status</th>
                            <th>Requested At</th>
                            <th>Processed At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>$<?php echo number_format($row['amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                                    <td><?php echo htmlspecialchars($row['payment_details']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td><?php echo $row['requested_at']; ?></td>
                                    <td><?php echo $row['processed_at'] ?? 'N/A'; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6">No withdrawal requests found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <!-- JavaScript to handle dynamic input field -->
    <script>
        const paymentMethod = document.getElementById('payment_method');
        const paymentDetailsLabel = document.getElementById('payment_details_label');
        const paymentDetailsInput = document.getElementById('payment_details');

        paymentMethod.addEventListener('change', function () {
            if (this.value) {
                paymentDetailsLabel.style.display = 'block';
                paymentDetailsInput.style.display = 'block';
                paymentDetailsInput.placeholder = getPlaceholder(this.value);
            } else {
                paymentDetailsLabel.style.display = 'none';
                paymentDetailsInput.style.display = 'none';
                paymentDetailsInput.value = '';
            }
        });

        function getPlaceholder(method) {
            switch (method) {
                case 'GCash': return 'Enter GCash number';
                case 'Bank Transfer': return 'Enter bank account number';
                case 'PayPal': return 'Enter PayPal email';
                case 'Maya': return 'Enter Maya number';
                default: return 'Enter details';
            }
        }
    </script>
</body>
</html>
