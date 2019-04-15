<?php
  $mysqli = new mysqli('localhost', 'rakesh', 'rakesh', 'messenger');
	if($mysqli->connect_errno) {
		printf("Connection failed: %s\n", $mysqli->connect.errno);
		exit();
	}
	function changepassword($username, $newpassword) {
  		$prepared_stmt = "UPDATE users SET password=password(?) WHERE username= ?;";
  		echo "DEBUG: prepared_stmt: $prepared_stmt";
  		if (!$stmt = $mysqli->prepare($prepared_stmt)) {
  			return FALSE;
  		}
  		$stmt->bind_param('ss', $newpassword, $username);
  		if(!$stmt->execute()) {
  			return FALSE;
  		}
		return TRUE;
	}
?>