<?php
session_start();
include "conn.php";

if (isset($_POST['acceptFriendRequest'])) {
    // Get the sender and receiver IDs from the form
    $senderUserId = $_POST['sender'];
    $receiverUserId = $_POST['receiver'];

    // Check if the current user is the receiver of the friend request
    if ($_SESSION['userID'] == $receiverUserId) {
        // Update the friendship status to 'accepted' in the database
        $updateQuery = "UPDATE friendships SET status = 'accepted' WHERE (user_id1 = ? AND user_id2 = ?) OR (user_id1 = ? AND user_id2 = ?)";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bind_param("iiii", $senderUserId, $receiverUserId, $receiverUserId, $senderUserId);

        if ($updateStmt->execute()) {
            header("Location: profile.php?id=$senderUserId");
        } else {
            
            echo "Error accepting friend request: " . $db->error;
        }

        $updateStmt->close();
    } else {
        echo "You can't accept friend requests on behalf of someone else.";
    }
} else {
    echo "Invalid request. Please try again.";
}
?>
