<?php
  require "session_auth.php";
  require "database.php";
  $nocsrftoken = $_POST["nocsrftoken"];
  echo "<script>alert('$nocsrftoken');</script>";
  if (!isset($nocsrftoken) or ($nocsrftoken != $_SESSION["nocsrftoken"])) {
    echo "<script>alert('CSRF detected.');</script>";
    header("Refresh:0; url=logout.php");
    die();
  }
  $username = $_SESSION["username"];
  $newpassword = $_POST["newpassword"];
  if (isset($newpassword)) {
    if (changepassword($username, $newpassword)) {
      echo "Password changed.";
    } else {
      echo "Password change failed.";
    }
  } else {
    echo "Provide new password to change";
    exit();
  }
?>