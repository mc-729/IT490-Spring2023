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

//sql to create sessions table

$sql = "CREATE TABLE IT490.ingredients(
	id INT NOT NULL AUTO_INCREMENT,
	name TINYTEXT NOT NULL,
  PRIMARY KEY (id))
  ";

if ($conn->query($sql) === TRUE) {
	echo "Table created successfully".PHP_EOL;
} else {
	echo "Error creating table: " .$conn->error;
}

$conn->close();
?>
