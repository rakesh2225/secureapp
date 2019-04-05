<?php
	session_start();
	if (isset($_POST["username"]) and isset($_POST["password"])) {
		if (checklogin($_POST["username"],$_POST["password"])) {
				$_SESSION["logged"] = TRUE;
				$_SESSION["username"] = $_POST["password"];
		} else {
			echo "<script>alert('Invalid username/password');</script>";
			unset($_SESSION["logged"]);
			header("Refresh:0; url=form.php");
			die();
		}
	}
	if (!isset($_SESSION["logged"]) or $_SESSION["logged"] != TRUE) {
		echo "<script>alert('You are not logged in. Pelase login first.');</script>";
		header("Refresh:0; url=form.php");
		die();
	}
?>
	<h2> Welcome <?php echo htmlentities($_SESSION['username']); ?> !</h2>
	<a ref="logout.php">Logout</a>
<?php		

	function checklogin($username, $password) {
		//init_set('display_errors', 'on');
		//error_reporting(E_ALL);
		echo "Username = $username password = $password";
		$mysqli = new mysqli('localhost', 'rakesh', 'rakesh', 'messenger');
		if($mysqli->connect_errno) {
			printf("Connection failed: %s\n", $mysqli->connect.errno);
			exit();
		}
		$sql = "SELECT * FROM users WHERE username='" . $username . "' ";
		$sql = $sql . " AND password = password('" . $password . "')";
		echo "DEBUG> SQL = $sql";
		$result = $mysqli->query($sql);
		if ($result->num_rows == 1) {
			return true;
		}
		return false;
  	}

  	function securelogin($username, $password) {
  		echo "Secure login";
  		$mysqli = new mysqli('localhost', 'rakesh', 'rakesh', 'messenger');
		if($mysqli->connect_errno) {
			printf("Connection failed: %s\n", $mysqli->connect.errno);
			exit();
		}
  		$prepared_stmt = "SELECT * FROM users WHERE username= ? AND password=password(?);";
  		if (!$stmt = $mysqli->prepare($prepared_stmt)) {
  			echo "Prepared statement error";
  		}
  		$stmt->bind_param('ss', $username, $password);
  		if(!$stmt->execute()) echo "Execution error";
  		if(!$stmt->store_result()) echo "Store results error";
  		$result = $stmt;
		if ($result->num_rows == 1) {
			return true;
		}
		return false;
	}
?>
