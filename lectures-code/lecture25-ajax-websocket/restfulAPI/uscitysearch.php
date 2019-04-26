<?php
	require 'database.php';
	// get thsity parameter from URL
	$query = $_REQUEST["city"];
	if(!isset($query)) exit; 
	$prepared_sql = "SELECT city, state, zip  FROM zips WHERE city LIKE ?;";
	//ensure that the database.php file exists and the $mysqli variable is defined there
  if(!$stmt = $mysqli->prepare($prepared_sql))
    echo "Prepared Statement Error";
	$query = "%".$query."%";     
  $stmt->bind_param('s', $query); 
  if(!$stmt->execute()) echo "Execute failed ";
  $city = NULL;
  $state = NULL;
  $zip = NULL;
  if(!$stmt->bind_result($city,$state,$zip)) echo "Binding failed ";
  //this will bind each row with the variables
  $num_rows = 0;
  while($stmt->fetch()){
    echo htmlentities($city) . ", " . htmlentities($state) . ", " . htmlentities($zip) . "<br>";
    $num_rows++;
  }
  if($num_rows==0) echo "No matching";
?>
