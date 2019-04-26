<?php
	session_set_cookie_params(15*30, "/minibook", "rakeshsv.secad.com", TRUE, TRUE);
  	session_start();
  	require "session_auth.php";
  	require "database.php";
  	echo "Add post";
  	/*$nocsrftoken = $_POST["nocsrftoken"];
	if (!isset($nocsrftoken) or ($nocsrftoken != $_SESSION["nocsrftoken"])) {
	    echo "<script>alert('CSRF detected.');</script>";
	    header("Refresh:0; url=logout.php");
	    die();
	}
	*/
	$json = file_get_contents('php://input');
	$data = json_decode($json);
	if (addnewpost($data->username, $data->post, $data->date)) {
		echo "<script>alert('HELLO')</script>";
	} else {
		echo "<script>handleFailedPost()</script>";
	}
?>