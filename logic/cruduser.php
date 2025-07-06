<?php
require_once '../db_connection.php';

function otentik($username, $password) {
    global $conn;

    $sql = "SELECT * FROM admin WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && md5($password) == $user['password']) {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id_admin'];
        $_SESSION['user_name_display'] = $user['nama'];
        $_SESSION['user_role'] = 'Admin';
        return true;
    }

    return false;
}
?>
