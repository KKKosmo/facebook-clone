<?php
session_start();
include "conn.php";

if (isset($_SESSION['username'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_post'])) {
        $userID = $_SESSION['userID'];
        $post_content = $_POST['post_content'];

        // Insert the post into the database
        $query = "INSERT INTO posts (user_id, post_content, post_date) VALUES (?, ?, NOW())";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ss", $userID, $post_content);
        $stmt->execute();
        $stmt->close();

        // Redirect back to the main page or wherever you want to display posts
        header('Location: dashboard.php');
        exit;
    }
} else {
    header('Location: index.php'); // Redirect to the login page if the user is not logged in
    exit;
}
?>