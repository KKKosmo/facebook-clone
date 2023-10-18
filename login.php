<?php
session_start();
include "conn.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT salt, password, user_id FROM users WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($salt, $dbPassword, $userID);
        $stmt->fetch();
        $hashedPassword = hash('sha256', $password . $salt);

        if ($hashedPassword === $dbPassword) {
            $_SESSION['username'] = $username;
            $_SESSION['userID'] = $userID;
            header('Location: dashboard.php');
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Invalid username " . $username;
    }

    $stmt->close();
    $db->close();
}
?>