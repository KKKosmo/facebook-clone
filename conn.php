<?php 
    $db = new mysqli('localhost', 'root', '', 'facebook');
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }
?>