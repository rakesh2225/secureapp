<?php
	session_set_cookie_params(15*30, "/minibook", "rakeshsv.secad.com", TRUE, TRUE);
	session_start();
  	require "database.php";
	require "models/post.php";
	if (isset($_POST["username"]) and isset($_POST["password"])) {
		if ($_POST["super"] == "on") {
			if (superLoginCheck($_POST["username"], $_POST["password"])) {
				$_SESSION["superlogged"] = TRUE;
				$rand = bin2hex(openssl_random_pseudo_bytes(16));
				$_SESSION["nocsrftoken"] = $rand;
				$_SESSION["superusername"] = $_POST["username"];
				$_SESSION["superbrowser"] = $_SERVER["HTTP_USER_AGENT"];
				header("Refresh:0; url=superuser.php?nocsrftoken=".$rand);
				die();
			} else {
				unset($_SESSION["superlogged"]);
				header("Refresh:0; url=form.php");
				die();
			}
			die();
		}
		if (securelogin($_POST["username"],$_POST["password"])) {
			$_SESSION["logged"] = TRUE;
			$_SESSION["username"] = $_POST["username"];
			$_SESSION["browser"] = $_SERVER["HTTP_USER_AGENT"];
			$rand = bin2hex(openssl_random_pseudo_bytes(16));
			$_SESSION["nocsrftoken"] = $rand;
		} else {
			echo "<script>alert('Invalid username/password or user is disabled');</script>";
			unset($_SESSION["logged"]);
			header("Refresh:0; url=form.php");
			die();
		}
	}	
	require "session_auth.php";
	if (isset($_POST["newpost"]) || isset($_GET["deletePostId"]) || isset($_POST["updatepost"])) {
		echo "CSRF check.";
		$nocsrftoken = $_REQUEST["nocsrftoken"];
		if (!isset($nocsrftoken) or ($nocsrftoken != $_SESSION["nocsrftoken"])) {
			echo "<script>alert('CSRF detected.');</script>";
			header("Refresh:0; url=form.php");
			die();
		}
	}
	$username = $_SESSION['username'];
	if (isset($_POST["newpost"])) {
		addnewpost($username, $_POST["newpost"], date("r"));
	} else if (isset($_GET["deletePostId"])) {
		deletePost($_GET["deletePostId"]);
	} else if (isset($_POST["updatepost"])) {
		updatepost($_POST["postid"], $_POST["updatepost"], date("r"));
	}
?>
	<!--<script type="text/javascript" src="posts.js"></script>
	-->
	<h2 align="center"> Welcome <?php echo htmlentities($username); ?> !</h2>
	<a align="center" href="changepwdform.php">Change password</a> | <a href="form.php">Logout</a></br></br>

	<form action="index.php" method="POST" class="form login">
        <h3>Posts</h3>
        <input type="hidden" name="nocsrftoken" value="<?php echo $_SESSION["nocsrftoken"]; ?>" /><br>
		<textarea name="newpost" title="Add new post" rows="5" cols="60"></textarea></br>        <button class="button" type="submit">
          Add Post
        </button>
  	</form>
	
	</br>
	<!--<button onClick="savePost('<?php echo $username ?>')">Add Post</button></br></br>
	-->
	<h3>All Posts</h3>

<?php
	echo renderPosts($username, $rand);
?>
