<?php
	//session_set_cookie_params(15 * 60, "/lab6", "rakeshsv.secad.com", TRUE, TRUE);
	session_start();    
	if(securelogin($_POST["username"], $_POST["password"])) {
?>
	<h2> Welcome <?php echo htmlentities($_POST['username']); ?> !</h2>
<?php		
	} else {
		echo "<script>alert('Invalid username/password');</script>";
		die();
	}
	function checklogin($username, $password) {
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
