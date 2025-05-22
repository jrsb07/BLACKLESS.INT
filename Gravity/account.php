<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT fullname, email, address FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($full_name, $email, $address);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Gravity</title>
    <link rel="stylesheet" href="account.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="user-profile">
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User Icon">
                <h3><?php echo htmlspecialchars($full_name); ?></h3>
                <p><?php echo htmlspecialchars($email); ?></p>
            </div>
            <ul>
                <li><a href="user_dashboard.php">Dashboard</a></li>
                <li><a href="orders.php">My Orders</a></li>
                <li><a href="account.php" class="active">Account</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        
        <main class="main-content">
            <header class="topbar">
                <h2>My Account</h2>
            </header>
            
            <section class="account-details">
                <h2 class="account-heading">Account Information</h2>
                <form class="account-form" action="update_account.php" method="POST">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($full_name); ?>">
                    
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">
                    
                    <label for="address">Shipping Address</label>
                    <textarea id="address" name="address"><?php echo htmlspecialchars($address); ?></textarea>
                    
                    <button type="submit" class="save-btn">Save Changes</button>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
