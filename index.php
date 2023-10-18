<!DOCTYPE html>
<html>
<head>
    <title>Login and Sign Up Page</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
	<div class="myHeader">
		<h1>Facebook</h1>
    </div>
	<div class="forms">
		<div class="login-container">
			<h1>Login</h1>
			<form action="login.php" method="post">
				<div class="input-container">
					<input type="text" name="username" placeholder="Username" required>
					<input type="password" name="password" placeholder="Password" required>
				</div>
				<button type="submit">Log In</button>
			</form>
		</div>
		<div class="signup-container">
			<h1>Sign Up</h1>
			<form action="signup.php" method="post">
				<div class="input-container">
					<input type="text" name="newUsername" placeholder="New Username" required>
					<input type="password" name="newPassword" placeholder="New Password" required>
				</div>
				<button type="submit">Sign Up</button>
			</form>
		</div>
	</div>

</body>
</html>