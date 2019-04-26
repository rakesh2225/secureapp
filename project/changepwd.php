<?php
  session_set_cookie_params(15*30, "/minibook", "rakeshsv.secad.com", TRUE, TRUE);
  session_start();
  require "session_auth.php";
  require "database.php";
  $nocsrftoken = $_POST["nocsrftoken"];
  if (!isset($nocsrftoken) or ($nocsrftoken != $_SESSION["nocsrftoken"])) {
    echo "<script>alert('CSRF detected.');</script>";
    header("Refresh:0; url=logout.php");
    die();
  }
  $username = $_SESSION["username"];
  $newpassword = $_POST["newpassword"];
  if (isset($newpassword)) {
    if (changepassword($username, $newpassword)) {
      echo "<script>alert('Password changed. Login again with the new password.');</script>";
      header("Refresh:0; url=form.php");
      die();
    } else {
      echo "Password change failed.";
    }
  } else {
    echo "Provide new password to change";
    exit();
  }
?>