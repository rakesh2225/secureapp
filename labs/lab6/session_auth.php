<?php
	session_set_cookie_params(15*30, "/lab6", "rakeshsv.secad.com", TRUE, TRUE);
	session_start();
	echo "<script>alert('You are not logged in. Pelase login first.');</script>";
	if (!isset($_SESSION["logged"]) or $_SESSION["logged"] != TRUE) {
		echo "<script>alert('You are not logged in. Pelase login first.');</script>";
		header("Refresh:0; url=form.php");
		die();
	}
	if ($_SESSION["browser"] != $_SERVER["HTTP_USER_AGENT"]) {
		echo "<script>alert('Session hijacking is detected.');</script>";
		header("Refresh:0; url=form.php");
		die();
	}
?>