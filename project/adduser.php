<?php
	require "database.php";
	$username = $_POST["username"];
	$password = $_POST["password"];
	if (!validateUsername($username) or !validatePassword($password)) {
		echo "Invalid username and password";
		die();
	}
	if (addnewuser($username, $password)) {
		echo "User $username registration successful.";
?>
	<a href="form.php">Login</a>
<?php
	} else {
		echo "Failed to register user $username.";
	}
	function validateUsername($username) {
		return TRUE;
	}

	function validatePassword($password) {
		return TRUE;
	}
?>