<?php
  require "session_auth.php";
  $rand = bin2hex(openssl_random_pseudo_bytes(16));
  $_SESSION["nocsrftoken"] = $rand;
?>
<html>
      <h1>Change password</h1>
<?php
  //some code here
  echo "Current time: " . date("Y-m-d h:i:sa")
?>
          <form action="changepwd.php" method="POST" class="form login">
                <input type="hidden" name="nocsrftoken" value="<?php echo $rand; ?>" />
                New Password: <input type="password" class="text_field" name="newpassword" /> <br>
                <button class="button" type="submit">
                  Change password
                </button>
          </form>
  </html>

