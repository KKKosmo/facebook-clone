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
    $stmt->close();  // Close the initial query statement

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (isset($_POST['update_profile'])) {
            $newDisplayName = $_POST['new_display_name'];

            // Update the display name in the database
            $updateQuery = "UPDATE users SET display_name = ? WHERE username = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bind_param("ss", $newDisplayName, $username);
            $updateStmt->execute();
            $updateStmt->close();  // Close the update statement

            // Update the current display_name variable
            $display_name = $newDisplayName;

            if ($_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'profile_pictures/';
                $uploadFile = $uploadDir . basename($_FILES['profile_picture']['name']);

                // Move the uploaded image to the specified directory
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
                    // Update the user's profile picture path in the database
                    $updateProfileQuery = "UPDATE users SET profile_picture = ? WHERE username = ?";
                    $updateProfileStmt = $db->prepare($updateProfileQuery);
                    $updateProfileStmt->bind_param("ss", $uploadFile, $username);
                    $updateProfileStmt->execute();
                    $updateProfileStmt->close();  // Close the profile picture update statement

                    // Redirect back to the profile page
                    header('Location: profile.php');
                    exit;
                } else {
                    echo "Failed to upload the image.";
                }
            } else {
                echo "Error uploading image.";
            }
        }
    }
} else {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="myHeader">
        <h1>Edit Your Profile</h1>
        <!-- Display the user's profile picture -->
        <img src="<?php echo $profile_picture_path; ?>" alt="Profile Picture" width="150">
    </div>

    <div class="profile-content">
        <h2>Edit Display Name</h2>
        <form action="profile.php" method="post" enctype="multipart/form-data">
            <label for="new_display_name">New Display Name:</label>
            <input type="text" id="new_display_name" name="new_display_name" value="<?php echo $display_name; ?>">
            <label for="profile_picture">Upload Profile Picture:</label>
            <input type="file" id="profile_picture" name="profile_picture">
            <button type="submit" name="update_profile">Update Profile</button>
        </form>

        <!-- You can add a form for profile picture upload here -->

        <!-- Button to redirect to the dashboard -->
        <a href="dashboard.php"><button>Go to Dashboard</button></a>
    </div>
    
    <!-- Include any other profile information editing forms as needed -->
</body>
</html>
