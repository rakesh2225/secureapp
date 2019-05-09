<?php
	session_set_cookie_params(15*30, "/minibook", "rakeshsv.secad.com", TRUE, TRUE);
  	session_start();
  	require "session_auth.php";
  	require "database.php";
  	require "models/post.php";
	$json = file_get_contents('php://input');
	$data = json_decode($json);
	if (addnewpost($data->username, $data->post, $data->date)) {
		renderPosts($data->username);
	} else {
		echo "<script>handleFailedPost()</script>";
	}
?>