<?php
require_once '../db_connection.php';

// Function fetch order history 
function fetchOrderSummary($conn) {
    $sql = "CALL GetOrderSummary()"; // Call the stored procedure
    return $conn->query($sql);
}

// Function delete order history
function deleteOrderHistory($conn) {
    try {
        $conn->begin_transaction();
        $conn->query("DELETE FROM order_items");
        $conn->query("DELETE FROM orders");
        $conn->commit();
        return "History cleared successfully.";
    } catch (Exception $e) {
        $conn->rollback();
        return "Failed: " . $e->getMessage();
    }
}

?>
