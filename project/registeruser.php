
<html>
      <h1>Register</h1>
<?php
  //some code here
  echo "Current time: " . date("Y-m-d h:i:sa")
?>
          <form action="adduser.php" method="POST" class="register_user">
                Username*:<input type="text" required 
                  pattern="^[\w.-]+@[\w-]+(.[\w-]+)*$" 
                  class="text_field" name="username" 
                  title="Enter your email address"
                  placeholder="Your email address"/> <br>

                Password*: <input type="password" required 
                  pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&])[\w!@#$%^&]{8,}$" class="text_field" 
                  name="password" 
                  title="Password must have atleast 8 characters with 1 special character !#$%@&^! and 1 number, 1 lowercase, 1 UPPERCASE characters." 
                  onchange="this.setCustomValidity(this.validity.patternMismatch?this.title:
                    ''); form.repassword.pattern = this.value;"
                  placeholder="Your password"
                /> <br>

                Confirm password*: <input type="password" required pattern="" 
                  class="text_field" name="repassword" 
                  title="Password does not match"
                  placeholder="Retype your password"
                  onchange="this.setCustomValidity(this.validity.patternMismatch?this.title: '');"
                  /> <br>
                <button class="button" type="submit">
                  Sign Up
                </button>
          </form>
  </html>

