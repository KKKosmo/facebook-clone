<?php
session_start();
include "conn.php";

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['croppedImage']) && $_FILES['croppedImage']['error'] === UPLOAD_ERR_OK) {
            // Generate a unique filename using a timestamp and a random string
            $fileExtension = pathinfo($_FILES['croppedImage']['name'], PATHINFO_EXTENSION);
            $uniqueFilename = time() . '_' . uniqid() . '.' . $fileExtension;
            $uploadPath = 'profile_pictures/' . $uniqueFilename; // Define your upload path
    
            $fileTmpPath = $_FILES['croppedImage']['tmp_name'];
    
            if (move_uploaded_file($fileTmpPath, $uploadPath)) {
                // Save the file to the server's filesystem
    
                $updateProfileQuery = "UPDATE users SET profile_picture = ? WHERE username = ?";
                $updateProfileStmt = $db->prepare($updateProfileQuery);
                $updateProfileStmt->bind_param("ss", $uploadPath, $username);
                
                if ($updateProfileStmt->execute()) {
                    $updateProfileStmt->close();
                    echo "Picture updated successfully.";
                } else {
                    echo "There was an error in updating the picture";
                }
            } else {
                echo 'Error uploading the file.';
            }
        } else {
            echo 'Invalid file data.';
        }
    }
} else {
    header('Location: index.php');
    exit;
}

?>
