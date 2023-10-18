<?php
session_start();
include "conn.php";

// Check if the 'id' parameter exists in the URL
if (isset($_GET['id'])) {
    $profileUserId = $_GET['id'];

    // Query the database to get the user's information based on the user ID
    $query = "SELECT user_id, username, display_name, profile_picture FROM users WHERE user_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $profileUserId);
    $stmt->execute();
    $stmt->bind_result($user_id, $username, $display_name, $profile_picture_path);
    $stmt->fetch();
    $stmt->close();

    // Query the database to get the user's posts
    $postQuery = "SELECT post_content, post_date FROM posts WHERE user_id = ? ORDER BY post_date DESC";
    $postStmt = $db->prepare($postQuery);
    $postStmt->bind_param("i", $profileUserId);
    $postStmt->execute();
    $postResult = $postStmt->get_result();
} else {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <?php
        $stylesheetPath = 'styles.css';
        $stylesheetVersion = filemtime($stylesheetPath); // Use the last modification time as the version
    ?>
    <link rel="stylesheet" type="text/css" href="styles.css?version=<?php echo $stylesheetVersion; ?>">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="myHeader">
        <?php
            if (isset($_SESSION['userID'])) {
                $loggedInUserId = $_SESSION['userID'];
            }
            if (isset($loggedInUserId) && $loggedInUserId != $profileUserId) {
                $friendRequestQuery = "SELECT * FROM friendships WHERE (user_id1 = ? AND user_id2 = ?) OR (user_id1 = ? AND user_id2 = ?)";
                $friendRequestStmt = $db->prepare($friendRequestQuery);
                $friendRequestStmt->bind_param("iiii", $loggedInUserId, $profileUserId, $profileUserId, $loggedInUserId);
                $friendRequestStmt->execute();
                $friendRequestResult = $friendRequestStmt->get_result();
                
                if ($friendRequestResult->num_rows === 0) {
                    echo '<form action="send_friend_request.php" method="post">';
                    echo '<input type="hidden" name="receiver" value="' . $profileUserId . '">';
                    echo '<input type="hidden" name="sender" value="' . $loggedInUserId . '">';
                    echo '<button type="submit" name="sendFriendRequest">Send Friend Request</button>';
                    echo '</form>';
                } else {
                    $row = $friendRequestResult->fetch_assoc();
                    $status = $row['status'];
                    if($status == 'pending'){
                        if($row['user_id2'] == $loggedInUserId){
                            echo '<form action="accept_friend_request.php" method="post">';
                            echo '<input type="hidden" name="sender" value="' . $profileUserId . '">';
                            echo '<input type="hidden" name="receiver" value="' . $loggedInUserId . '">';
                            echo '<button type="submit" name="acceptFriendRequest">Accept Friend Request</button>';
                            echo '</form>';
                        } 
                        else{
                            echo '<form action="cancel_friend_request.php" method="post">';
                            echo '<input type="hidden" name="sender" value="' . $loggedInUserId . '">';
                            echo '<input type="hidden" name="receiver" value="' . $profileUserId . '">';
                            echo '<button type="submit" name="cancelPending">Cancel Pending Friend Request</button>';
                            echo '</form>';
                        }
                    }
                    else if($status == 'accepted'){
                        echo '<form action="unfriend.php" method="post">';
                        echo '<input type="hidden" name="receiver" value="' . $profileUserId . '">';
                        echo '<input type="hidden" name="sender" value="' . $loggedInUserId . '">';
                        echo '<button type="submit" name="unfriend">Unfriend</button>';
                        echo '</form>';
                    }
                }
            }
        ?>

        <h1 class="user-name">Profile of <?php echo $display_name; ?></h1>
        <img src="<?php echo $profile_picture_path; ?>" alt="Profile Picture" class="profile-picture">
        <?php
            if (isset($_SESSION['username']) && $_SESSION['username'] == $username) {
                echo '<a href="editProfile.php"><button>Edit Profile</button></a>';
            }
        ?>
    </div>



    <div class="profile-content">
        <!-- Display user's posts -->
        <h2>Posts:</h2>
        <div class='post'>
            <?php
                while ($row = $postResult->fetch_assoc()) {
                    echo "<p>Posted on: {$row['post_date']}</p>";
                    echo "<p>{$row['post_content']}</p>";
                }
            ?>
        </div>

    </div>



</body>
</html>
