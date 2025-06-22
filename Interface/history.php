<?php
session_start();
require_once '../logic/logic_history.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="../css/history.css">
</head>
<body>
    <div class="container">
        <!-- Header with title and delete button -->
        <div class="header">
            <h1 class="title">📋 Order History</h1>
            <div class="action-buttons">
                <form method="post" onsubmit="return confirm('Are you sure you want to delete ALL history? This cannot be undone!');">
                    <button type="submit" name="delete_all" class="btn btn-delete">
                        🗑️ Clear All History
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message success">
                <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error">
                <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <!-- Search Form -->
        <form method="get" class="search-form">
            <input 
                type="text" 
                name="search" 
                class="search-input"
                placeholder="Search by order ID, customer ID, or amount..."
                value="<?php echo htmlspecialchars($search); ?>"
            >
            <button type="submit" class="btn btn-search">🔍 Search</button>
            <?php if (!empty($search)): ?>
                <a href="?" class="btn btn-reset">🔄 Reset</a>
            <?php endif; ?>
        </form>
        
        <!-- History Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Order ID</th>
                    <th>Customer ID</th>
                    <th>Total Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($historyData)): ?>
                    <tr>
                        <td colspan="5" class="empty-message">
                            No order history found
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($historyData as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['pelanggan_id']); ?></td>
                            <td><?php echo 'Rp ' . number_format($row['total_harga'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($row['tanggal']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
