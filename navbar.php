<nav>
    <ul>
        <li><a <?php echo (strpos($_SERVER['REQUEST_URI'], 'dashboard.php') !== false) ? 'class="active"' : ''; ?> href="dashboard.php">Dashboard</a></li>
        <?php
        if (isset($_SESSION['userID'])) {
            $userID = $_SESSION['userID'];
            $profileLink = 'profile.php?id=' . $userID;
            
            // Check if the user is on their own profile page
            $isOwnProfile = (isset($_GET['id']) && $_GET['id'] == $userID);
            $profileClass = ($isOwnProfile && strpos($_SERVER['REQUEST_URI'], 'profile.php') !== false) ? 'class="active"' : '';

            echo '<li><a ' . $profileClass . ' href="' . $profileLink . '">Profile</a></li>';
            echo '<li class="logout-button"><a href="logout.php">Logout</a></li>';
        }
        ?>
    </ul>
</nav>