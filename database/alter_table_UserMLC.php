#!/usr/bin/php
<?php
$servername = "localhost";
$username = "testuser";
$password = "12345";
$dbname = "IT490";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else 
{
	echo "Successfully Connected!".PHP_EOL;
}

// sql to alter table UserMLC
$sql = "ALTER TABLE IT490.UserMLC
MODIFY COLUMN Amount INT";
// or try Amount BIGINT just replace the datatype
if ($conn->query($sql) === TRUE) {
  echo "Table UserMLC altered successfully".PHP_EOL;
} else {
  echo "Error altering table UserMLC : " . $conn->error;
}

$conn->close();
?>