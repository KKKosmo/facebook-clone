<?php
session_start();
include "conn.php";

if (isset($_POST['unfriend'])) {
    $sender = $_POST['sender'];
    $receiver = $_POST['receiver'];

    // Delete the friendship record
    $deleteQuery = "DELETE FROM friendships WHERE (user_id1 = ? AND user_id2 = ?) OR (user_id1 = ? AND user_id2 = ?)";
    $deleteStmt = $db->prepare($deleteQuery);
    $deleteStmt->bind_param("iiii", $sender, $receiver, $receiver, $sender);

    if ($deleteStmt->execute()) {
        // Unfriending was successful
        // You can redirect or display a success message here
        header('Location: profile.php?id=' . $receiver); // Redirect to the profile page of the user being unfriended
        exit;
    } else {
        // Error handling if the unfriending process fails
        echo "Failed to unfriend the user.";
    }
} else {
    // Handle cases where the 'unfriend' button was not clicked
    header('Location: index.php'); // Redirect to the index page or any other desired location
    exit;
}
?>
