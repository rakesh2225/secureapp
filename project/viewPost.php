<?php
	session_set_cookie_params(15*30, "/minibook", "rakeshsv.secad.com", TRUE, TRUE);
	session_start();
	require "session_auth.php";
	require "database.php";
	require "models/post.php";
	require "models/comment.php";
	$nocsrftoken = $_REQUEST["nocsrftoken"];
	if (!isset($nocsrftoken) or ($nocsrftoken != $_SESSION["nocsrftoken"])) {
		echo "<script>alert('CSRF detected.');</script>";
		header("Refresh:0; url=logout.php");
		die();
	}
	if (!isset($_REQUEST["postid"])) {
		echo "<script>alert('Invalid postId');</script>";
	    header("Refresh:0; url=form.php");
	    die();
	}
	$postid = $_REQUEST["postid"];
	$post = getPost($postid);
	if (isset($_REQUEST["deleteCommentId"])) {
		deleteComment($_REQUEST["deleteCommentId"]);
	} else if (isset($_POST["addcomment"])) {
		addnewcomment($_SESSION["username"], $_POST["addcomment"], $_POST["postid"], date("r"));
	}
	$superUser = isSuperUser($_SESSION["username"], 1); 
 ?>
 <h2 align="center"> Welcome <?php echo htmlentities($_SESSION["username"]); ?> !</h2>
 <a align="center" href="changepwdform.php">Change password</a> | <a href="form.php">Logout</a></br></br>
 	<form action="index.php" method="POST" class="form login">
 		<input type="hidden" name="nocsrftoken" value="<?php echo $nocsrftoken; ?>" /><br>
 		<input type="hidden" name="postid" value="<?php echo $postid; ?>" /><br>
		<textarea name="updatepost" title="Update post" rows="5" cols="60"><?php echo $post->post;?></textarea></br>
<?php
		if ($superUser || ($_SESSION["username"] == $post->username)) {
?>
			<button class="button" type="submit">Update Post</button>
<?php
		}
        
?>
  	</form>
<?php
	echo renderComments($postid, $_SESSION["username"]);
?>