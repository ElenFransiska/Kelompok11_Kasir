<?php
require '../db_connection.php';

class MenuController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getMenuItems() {
        $sql = "SELECT * FROM menu";
        $result = $this->conn->query($sql);
        
        if (!$result) {
            throw new Exception("Database error: " . $this->conn->error);
        }

        $menuItems = [
            'makanan' => [],
            'minuman' => []
        ];

        while ($row = $result->fetch_assoc()) {
            $category = strtolower($row['jenis']);
            $menuItems[$category][] = $row;
        }

        return $menuItems;
    }

    public function handleOrder($postData) {
        // Validate and process order
        if (empty($postData['items'])) {
            throw new Exception("No items in order");
        }

        // Add your order processing logic here
        // This is just a placeholder implementation
        return [
            'success' => true,
            'order_id' => uniqid(),
            'message' => 'Order processed successfully'
        ];
    }
}

// Initialize controller
try {
    $controller = new MenuController($conn);
    $menuData = $controller->getMenuItems();
    
    // Pass data to view
    require '../view/menu_view.php';
    
} catch (Exception $e) {
    // Handle errors gracefully
    error_log($e->getMessage());
    header("HTTP/1.1 500 Internal Server Error");
    die("An error occurred while loading the menu. Please try again later.");
}
?>
