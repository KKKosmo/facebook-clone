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
} else {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="myHeader">
        <h1>Welcome, <?php echo $display_name; ?>!</h1>
        <!-- Display the user's profile picture -->
        <img src="<?php echo $profile_picture_path; ?>" alt="Profile Picture" width="150">
    </div>

    <div class="dashboard-content">
        <p>This is your personalized dashboard. You can add content and features relevant to your application.</p>

        <!-- Profile Button -->
        <a href="profile.php"><button>Edit Profile</button></a>

        <!-- Logout Button -->
        <form action="logout.php" method="post">
            <button type="submit" name="logout">Logout</button>
        </form>
    </div>
</body>
</html>
