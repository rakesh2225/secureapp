<?php
	session_set_cookie_params(15*30, "/minibook", "rakeshsv.secad.com", TRUE, TRUE);
	session_start();
	require "database.php";
	require "models/user.php";
	if (!isset($_SESSION["superlogged"]) or $_SESSION["superlogged"] != TRUE) {
		echo "<script>alert('You are not logged in. Pelase login first.');</script>";
		header("Refresh:0; url=form.php");
		die();
	}
	if ($_SESSION["superbrowser"] != $_SERVER["HTTP_USER_AGENT"]) {
		echo "<script>alert('Session hijacking is detected.');</script>";
		header("Refresh:0; url=form.php");
		die();
	}
	/*	
	$nocsrftoken = $_REQUEST["nocsrftoken"];
	if (!isset($nocsrftoken) or ($nocsrftoken != $_SESSION["nocsrftoken"])) {
		echo "<script>alert('CSRF detected.');</script>";
		header("Refresh:0; url=logout.php");
		die();
	}*/
	session_set_cookie_params(15*30, "/minibook", "rakeshsv.secad.com", TRUE, TRUE);
	session_start();
	$rand = bin2hex(openssl_random_pseudo_bytes(16));
	$_SESSION["nocsrftoken"] = $rand;
	if (isset($_POST["saveForm"])) {
		$enabled = 0;
		$checked = $_POST["1"];
		if (isset($_POST["1"]) && $checked == "on") {
			$enabled = 1;
		}
		enableUser("raghu@gmail.com", $enabled);
		$checked = $_POST["2"];
		$enabled = 0;
		if (isset($_POST["2"]) && $checked == "on") {
			$enabled = 1;
		}
		enableUser("rakesh@gmail.com", $enabled);
	}
?>
<html>
    <h2> Welcome <?php echo $_SESSION["superusername"]; ?>! </h2>
    <a href="form.php">Logout</a></br>
	<?php
		echo renderSuper();
	?>
</html>