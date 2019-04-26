<?php
  $mysqli = new mysqli('localhost', 'rakesh', 'rakesh', 'messenger');
	if($mysqli->connect_errno) {
		printf("Connection failed: %s\n", $mysqli->connect.errno);
		exit();
	}
	function changepassword($username, $newpassword) {
      global $mysqli;
  		$prepared_stmt = "UPDATE users SET password=password(?) WHERE username= ?;";
  		if (!$stmt = $mysqli->prepare($prepared_stmt)) {
  			return FALSE;
  		}
  		$stmt->bind_param('ss', $newpassword, $username);
  		if(!$stmt->execute()) {
  			return FALSE;
  		}
		  return TRUE;
	}
  function addnewuser($username, $newpassword) {
      global $mysqli;
      $superUser = 0;
      $enabled = 1;
      $prepared_stmt = "INSERT INTO users VALUES(?, password(?), ?, ?);";
      if (!$stmt = $mysqli->prepare($prepared_stmt)) {
        return FALSE;
      }
      $stmt->bind_param('ssii', $username, $newpassword, $superUser, $enabled);
      if(!$stmt->execute()) {
        return FALSE;
      }
      return TRUE;
  }
  function addnewpost($username, $post, $date) {
      global $mysqli;
      echo "INSERT INTO POST";
      $prepared_stmt = "INSERT INTO posts(username, post, posted_date) VALUES(?, ?, ?);";
      if (!$stmt = $mysqli->prepare($prepared_stmt)) {
        return FALSE;
      }
      $stmt->bind_param('sss', $username, $post, $date);
      if(!$stmt->execute()) {
        return FALSE;
      }
      return TRUE;
  }
?>