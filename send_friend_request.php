<?php
session_start();
include "conn.php";

if (isset($_POST['sendFriendRequest'])) {
    // Get the sender and receiver IDs from the form submission
    $senderId = $_POST['sender'];
    $receiverId = $_POST['receiver'];

    // Insert the friend request into the 'friendships' table with a status of 'pending'
    $insertQuery = "INSERT INTO friendships (user_id1, user_id2, status) VALUES (?, ?, 'pending')";
    $insertStmt = $db->prepare($insertQuery);
    $insertStmt->bind_param("ii", $senderId, $receiverId);
    $insertStmt->execute();

    // Redirect back to the profile page
    header("Location: profile.php?id=$receiverId");
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>
