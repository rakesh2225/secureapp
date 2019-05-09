<?php

class Post{
  public $postid, $post, $username, $posted_date;
  function __construct($postid, $username, $post, $posted_date){
    $this->postid= $postid;
    $this->post = $post;
    $this->posted_date= $posted_date;
    $this->username = $username;
  }

  
/*
  function render() {
    $str.= '<div class="inner" align="center">';
    $str.= "<p>".htmlentities($this->text)."</p></div>";   
    $str.= "<p><a href=\"/post.php?id=".h(htmlentities($this->postid))."\">";
    $str.= $this->render_comments();
    $str.= "</a></p>";
    return $str;
  }
  function add_comment() {
    global $dblink;
    $sql  = "INSERT INTO comments (title,author, text, post_id) values ('";
    $sql .= mysqli_real_escape_string($dblink, htmlspecialchars($_POST["title"]))."','";
    $sql .= mysqli_real_escape_string($dblink, htmlspecialchars($_POST["author"]))."','";
    $sql .= mysqli_real_escape_string($dblink, htmlspecialchars($_POST["text"]))."',";
    $sql .= intval($this->id).")";
    $result = mysqli_query($dblink, $sql);
    echo mysqli_error(); 
  } 
  function render_comments() {
    $str.= '<div class="inner" style="padding-left: 40px;">';
    $str.= "<p>".htmlentities($this->post)."</p></div>";   
    $str.= "\n\n<div class='comments'><h3>Comments: </h3>\n<ul>";
    foreach ($this->get_comments() as $comment) {
      $str.= "\n\t<li>".htmlentities($comment->text)."</li>";
    }    
    $str.= "\n</ul></div>\n";
    $str.= '<div class="inner" style="padding-left: 40px;">';
    $str.= "\n<h3>Add comment: </h3>\n";  
    $str.= "\n<textarea id=\"".h($this->id)."_comment\" title=\"Add new post\" rows=\"5\" cols=\"60\"></textarea>\n"; 
    
    return $str;
  }
  */
 /*
  function get_comments() {
    global $mysqli;
    $comments = Array();
    $results = mysqli_query($dblink, "SELECT * FROM comments where post_id=".$this->id);
    if (isset($results)){
      while ($row = mysqli_fetch_assoc($results)) {
        $comments[] = Comment::from_row($row);
      }
    }
    return $comments;
  } 
 */
}

function renderPosts($username) {
    $str = '<div class="inner" align="left">'; 
    $superUser = isSuperUser($username, 1); 
    foreach (getAllPosts() as $post) {
      $str.= "\n\t<div style='background-color: #AEB6BF;padding:3px;margin:2px'><i>".htmlentities($post->username)." | ".htmlentities($post->posted_date)."</i>";
      $str.= "\n\t<p>".htmlentities($post->post)."</p>";
      if ($superUser || ($username == $post->username)) {
        $str.= "\n<i><a href=\"index.php?nocsrftoken=".$_SESSION["nocsrftoken"]."&deletePostId=".htmlentities($post->postid)."\">Delete</a> | ";        
      }
      $str.= "<a href=\"viewPost.php?nocsrftoken=".$_SESSION["nocsrftoken"]."&postid=".htmlentities($post->postid)."\">View</a></i></div>\n";
    }
    $str.= "</div>";
    return $str;
  }

  function getAllPosts() {
      global $mysqli;
      $sql = "SELECT * FROM posts order by posted_date desc;";
      $results= mysqli_query($mysqli, $sql);
      $posts = Array();
      while ($row = mysqli_fetch_assoc($results)) {
        $posts[] = new Post($row['postid'], $row['username'], $row['post'], $row['posted_date']);
      }
      return $posts;
  }

  function getAllPostsByUser($username) {
      global $mysqli;
      $prepared_stmt = "SELECT * FROM posts WHERE username=? order by posted_date desc;";
      if (!$stmt = $mysqli->prepare($prepared_stmt)) {
        return FALSE;
      }
      $stmt->bind_param('s', $username);
      if(!$stmt->execute()) {
        return FALSE;
      }
      $posts = Array();
      $result = $stmt->get_result();
      while ($row = mysqli_fetch_assoc($result)) {
        $posts[] = new Post($row['postid'], $row['username'], $row['post'], $row['posted_date']);
      }
      return $posts;
  }

  function getPost($postid) {
      global $mysqli;
      $prepared_stmt = "SELECT * FROM posts WHERE postid=?;";
      if (!$stmt = $mysqli->prepare($prepared_stmt)) {
        return FALSE;
      }
      $stmt->bind_param('i', $postid);
      if(!$stmt->execute()) {
        return FALSE;
      }
      $result = $stmt->get_result();
      if(!$result) {
        return FALSE;
      }
      $row = mysqli_fetch_assoc($result);
      $post = new Post($row['postid'], $row['username'], $row['post'], $row['posted_date']);
      return $post;
  }

?>
