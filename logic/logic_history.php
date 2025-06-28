<?php
require_once '../db_connection.php';

function fetchOrderSummary($conn) {
    $sql = "CALL GetOrderSummary()"; // Call the stored procedure
    return $conn->query($sql);
}
?>
