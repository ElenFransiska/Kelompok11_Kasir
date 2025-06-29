<?php
require_once '../db_connection.php';

function fetchOrderSummary($conn) {
    $sql = "CALL GetOrderSummary()"; // Call the stored procedure
    return $conn->query($sql);
}

// Function to delete order history
function deleteOrderHistory($conn) {
    // Start a transaction
    $conn->begin_transaction();

    try {
        // Delete from order_items first due to foreign key constraint
        $conn->query("DELETE FROM order_items");
        // Then delete from orders
        $conn->query("DELETE FROM orders");

        // Commit the transaction
        $conn->commit();
        return "History cleared successfully.";
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        return "Failed to clear history: " . $e->getMessage();
    }
}
?>
