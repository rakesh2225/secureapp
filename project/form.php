<html>
      <h1>Login</h1>
<?php
  session_start();
  session_destroy();
  
  echo "Current time: " . date("Y-m-d h:i:sa")
?>
    <form action="index.php" method="POST" class="form login">
      Username   :<input type="text" class="text_field" name="username" /> <br>
      Password   : <input type="password" class="text_field" name="password" /> <br>
      Superuser? : <input type="checkbox" class="text_field" name="super" /> </br></br>
      <button class="button" type="submit"> Login </button>
      <a href="registeruser.php">Register</a>
    </form>
  </html>

