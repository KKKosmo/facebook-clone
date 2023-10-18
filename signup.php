<?php
include "conn.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = $_POST['newUsername'];
    $newPassword = $_POST['newPassword'];

    // Check if the username is available (not already in use)
    $query = "SELECT username FROM users WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $newUsername);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Username already in use. Please choose a different username.";
    } else {
        // Generate a random salt
        $salt = bin2hex(random_bytes(16));

        // Combine the password with the salt and hash it
        $hashedPassword = hash('sha256', $newPassword . $salt);

        // Insert user data into the database
        $query = "INSERT INTO users (username, password, salt, display_name) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssss", $newUsername, $hashedPassword, $salt, $newUsername);

        if ($stmt->execute()) {
            echo "Registration successful. You can now log in.";
        } else {
            echo "Registration failed. Please try again.";
        }
    }

    $stmt->close();
    $db->close();
}
?>
