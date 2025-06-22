<?php
require_once '../db_connection.php';

// Handle delete all request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_all'])) {
    $sql = "DELETE FROM history";
    if ($koneksi->query($sql)) {
        $message = "All history has been cleared successfully!";
        $_SESSION['message'] = $message;
        header("Location: ../interface/history.php");
        exit();
    } else {
        $error = "Error clearing history: " . $koneksi->$error;
        $_SESSION['error'] = $error;
    }
}

// Get history data
function getHistory($koneksi, $search = '') {
    $sql = "SELECT * FROM history";
    
    if (!empty($search)) {
        $search = $koneksi->real_escape_string($search);
        $sql .= " WHERE 
                order_id LIKE '%$search%' OR
                pelanggan_id LIKE '%$search%' OR
                total_harga LIKE '%$search%'";
    }
    
    $sql .= " ORDER BY tanggal DESC, id DESC";
    
    $result = $koneksi->query($sql);
    $data = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    
    return $data;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$historyData = getHistory($koneksi, $search);
?>
