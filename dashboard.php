<?php
session_start();
include "conn.php";

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Query the database to get the display name and profile picture path based on the username
    $query = "SELECT display_name, profile_picture FROM users WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($display_name, $profile_picture_path);
    $stmt->fetch();
    $stmt->close();  // Close the query statement


    $query = "SELECT p.user_id, p.post_content, p.post_date, u.display_name, u.profile_picture FROM posts p inner join users u on p.user_id = u.user_id ORDER BY post_date DESC";
    $result = mysqli_query($db, $query);
    if ($result) {
        $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        echo "Error: " . mysqli_error($db);
        $posts = [];
    }

    

} else {
    header('Location: index.php');
    exit;
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <?php
        $stylesheetPath = 'styles.css';
        $stylesheetVersion = filemtime($stylesheetPath); // Use the last modification time as the version
    ?>
    <link rel="stylesheet" type="text/css" href="styles.css?version=<?php echo $stylesheetVersion; ?>">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="myHeader">
        <h1 class="user-name"><?php echo $display_name; ?></h1>
        <img src="<?php echo $profile_picture_path; ?>" alt="Profile Picture" class="profile-picture">
    </div>

    <div class="dashboard-content">
        <p>This is your personalized dashboard. You can add content and features relevant to your application.</p>
        <div class="post-content">
            <form action="create_post.php" method="post" class="post-form">
                <label for="post_content">Write your post:</label>
                <textarea id="post_content" name="post_content" rows="4" required></textarea>
                <button type="submit" name="submit_post">Post</button>
            </form>
        </div>

            <div class="post-container">
                <?php
                // Loop through and display posts
                foreach ($posts as $post) {
                    $profileLink = "profile.php?id=" . $post['user_id']; // Assuming 'user_id' is the user identifier in the posts array
                    echo "<div class='post'>";
                    echo "<div class='post-header'>";
                    echo "<img src='{$post['profile_picture']}' alt='Profile Picture' class='post-profile-picture'>";
                    echo "<p><a class='post-display-name' href='$profileLink'>{$post['display_name']}</a></p>";
                    echo "<p>Posted on: {$post['post_date']}</p>";
                    echo "</div>";
                    echo "<p>{$post['post_content']}</p>";
                    echo "</div>";
                }
                ?>
            </div>


        </div>
    </div>
</body>
</html>
