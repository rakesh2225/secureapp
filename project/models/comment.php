<?php

class Comment {

  public $commentid, $postid, $comment, $username, $commented_date;
  function __construct($commentid, $postid, $comment, $username, $commented_date) {
    $this->postid = $postid;
    $this->commentid = $commentid;
    $this->comment = $comment;
    $this->username = $username;
    $this->commented_date = $commented_date;
  }

}

  function renderComments($postid, $loginUser) {
    $str = '<div class="inner" align="left" style="background-color: #AEB6BF;padding:3px;margin:2px">';
    $str.="<form action='viewPost.php' method='POST' class='form login'>";
    $str.="<input type='hidden' name='postid' value=".$postid." /><br>";
    $str.="<input type='hidden' name='nocsrftoken' value=".$_SESSION["nocsrftoken"]." /><br>";
    $str.="<textarea name=\"addcomment\" title=\"Add comment\" rows=\"5\" cols=\"60\"></textarea></br>";
    $superUser = isSuperUser($loginUser, 1); 
    $str.="<button class=\"button\" type=\"submit\">Add Comment</button></br>";
    $str.="</form>";
    foreach (getCommentsByPostId($postid) as $comment) {
      $str.= "\n\t\t<div style='background-color: #E5E8E8;padding:3px;margin:2px'><i>".htmlentities($comment->username)." | ".htmlentities($comment->commented_date)."</i>";
      $str.= "\n\t\t<p>".htmlentities($comment->comment)."</p>";
      if ($superUser || ($comment->username == $loginUser)) {
        $str.= "\n<i><a href=\"viewPost.php?nocsrftoken=".$_SESSION["nocsrftoken"]."&postid=".htmlentities($postid)."&deleteCommentId=".htmlentities($comment->commentid)."\">Delete</a> | ";
      }
      //$str.= "<a href=\"viewPost.php?postid=".htmlentities($comment->commentid)."\">View</a></i>";
      $str.="</div>\n";
    }
    $str.= "</div>";
    return $str;
  }

  function getCommentsByPostId($postid) {
    global $mysqli;
    $prepared_stmt = "SELECT * FROM comments WHERE postid=? order by commented_date desc;";
      if (!$stmt = $mysqli->prepare($prepared_stmt)) {
        return FALSE;
      }
      $stmt->bind_param('i', $postid);
      if(!$stmt->execute()) {
        return FALSE;
      }
      $comments = Array();
      $result = $stmt->get_result();
      while ($row = mysqli_fetch_assoc($result)) {
        $comments[] = new Comment($row['commentid'], $row['postid'], $row['comment'], $row['username'], $row['commented_date']);
      }
      return $comments;
  }
?>
