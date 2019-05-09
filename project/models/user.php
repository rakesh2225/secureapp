<?php

class User {

  public $username, $password, $enabled, $super;
  function __construct($username, $password, $enabled, $super) {
    $this->super = $super;
    $this->enabled = $enabled;
    $this->password = $password;
    $this->username = $username;
  }

}


  function renderSuper() {
  	$str ="<form action=\"superuser.php\" method=\"POST\" class=\"form login\"><table>";
  	$str.="<input type='hidden' name=\"saveForm\"></input>\n\t";
    $str.="<input type='hidden' name='nocsrftoken' value=".$_SESSION["nocsrftoken"]." /><br>";      
    $count=0;
    foreach (getAllUsers() as $user) {
      $checked = "";
      $count = $count + 1;
      if ($user->enabled == 1) {
		$checked = "checked";
      }
      $str.="<tr><td>$user->username</td> <td><input type='checkbox' $checked name=".$count."></input></td>";
    }    
    $str.= "</table>";
    $str.="</br><button class=\"button\" type=\"submit\">Save</button></br></form>";
    return $str;
  }

  function getAllUsers() {
      global $mysqli;
      $sql = "SELECT * FROM users WHERE super=0 order by username;";
      $results= mysqli_query($mysqli, $sql);
      $users = Array();
      while ($row = mysqli_fetch_assoc($results)) {
        $users[] = new User($row['username'], $row['password'], $row['enabled'], $row['super']);
      }
      return $users;
  }