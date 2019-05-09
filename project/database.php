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
      $stmt->bind_param('ssii', $username, $newpassword, $enabled, $superUser);
      if(!$stmt->execute()) {
        return FALSE;
      }
      return TRUE;
  }

  function addnewpost($username, $post, $date) {
      global $mysqli;
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

  function deletePost($postid) {
      global $mysqli;
      $prepared_stmt = "DELETE FROM posts WHERE postid=?;";
      if (!$stmt = $mysqli->prepare($prepared_stmt)) {
        return FALSE;
      }
      $stmt->bind_param('i', $postid);
      if(!$stmt->execute()) {
        return FALSE;
      }
      return TRUE;
  }

  function updatePost($postid, $post, $date) {
      global $mysqli;
      $prepared_stmt = "UPDATE posts set post=?, posted_date=? WHERE postid=?;";
      if (!$stmt = $mysqli->prepare($prepared_stmt)) {
        return FALSE;
      }
      $stmt->bind_param('ssi', $post, $date, $postid);
      if(!$stmt->execute()) {
        return FALSE;
      }
      return TRUE;
  }

  function addnewcomment($username, $comment, $postid, $date) {
      global $mysqli;
      $prepared_stmt = "INSERT INTO comments(username, comment, postid, commented_date) VALUES(?, ?, ?, ?);";
      if (!$stmt = $mysqli->prepare($prepared_stmt)) {
        return FALSE;
      }
      $stmt->bind_param('ssss', $username, $comment, $postid, $date);
      if(!$stmt->execute()) {
        return FALSE;
      }
      return TRUE;
  }

  function deleteComment($commentid) {
      global $mysqli;
      $prepared_stmt = "DELETE FROM comments WHERE commentid=?;";
      if (!$stmt = $mysqli->prepare($prepared_stmt)) {
        return FALSE;
      }
      $stmt->bind_param('i', $commentid);
      if(!$stmt->execute()) {
        return FALSE;
      }
      return TRUE;
  }

  function isSuperUser($username, $super) {
      $mysqli = new mysqli('localhost', 'rakesh', 'rakesh', 'messenger');
      if($mysqli->connect_errno) {
        printf("Connection failed: %s\n", $mysqli->connect.errno);
        exit();
      }
      $prepared_stmt = "SELECT * FROM users WHERE username= ? AND super=?;";
      if (!$stmt = $mysqli->prepare($prepared_stmt)) {
        echo "Prepared statement error";
        return false;
      }
      $stmt->bind_param('si', $username, $super);
      if(!$stmt->execute()) {
        echo "Execution error";
        return false;
      }
      if(!$stmt->store_result()) {
        echo "Store results error";
        return false;
      }
      $result = $stmt;
      if ($result->num_rows > 0) {
        return true;
      }
      return false;
  }

  function securelogin($username, $password) {
      $mysqli = new mysqli('localhost', 'rakesh', 'rakesh', 'messenger');
      if($mysqli->connect_errno) {
        printf("Connection failed: %s\n", $mysqli->connect.errno);
        exit();
      }
      $normalUser = 0;
      $prepared_stmt = "SELECT * FROM users WHERE username= ? AND password=password(?) AND enabled=? AND super = ?;";
      if (!$stmt = $mysqli->prepare($prepared_stmt)) {
        echo "Prepared statement error";
        return false;
      }
      $enabled=1;
      $stmt->bind_param('ssii', $username, $password, $enabled, $normalUser);
      if(!$stmt->execute()) {
        echo "Execution error";
        return false;
      }
      if(!$stmt->store_result()) {
        echo "Store results error";
        return false;
      }
      $result = $stmt;
    if ($result->num_rows == 1) {
      return true;
    }
    return false;
  }

  function superLoginCheck($username, $password) {
    $mysqli = new mysqli('localhost', 'rakesh', 'rakesh', 'messenger');
      if($mysqli->connect_errno) {
        printf("Connection failed: %s\n", $mysqli->connect.errno);
        exit();
      }
      $superuser = 1;
      $prepared_stmt = "SELECT * FROM users WHERE username= ? AND password=password(?) AND super = ?;";
      if (!$stmt = $mysqli->prepare($prepared_stmt)) {
        echo "Prepared statement error";
        return false;
      }
      $stmt->bind_param('ssi', $username, $password, $superuser);
      if(!$stmt->execute()) {
        echo "Execution error";
        return false;
      }
      if(!$stmt->store_result()) {
        echo "Store results error";
        return false;
      }
      $result = $stmt;
    if ($result->num_rows == 1) {
      return true;
    }
    return false;
  }

  function enableUser($username, $enabled) {
      global $mysqli;
      $prepared_stmt = "UPDATE users set enabled=? WHERE username=?;";
      if (!$stmt = $mysqli->prepare($prepared_stmt)) {
        return FALSE;
      }
      $stmt->bind_param('is', $enabled, $username);
      if(!$stmt->execute()) {
        return FALSE;
      }
      return TRUE;
  }

?>