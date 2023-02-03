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

// sql to create table Users
$sql = "CREATE TABLE IT490.Users(
	User_ID INT NOT NULL AUTO_INCREMENT,
	F_Name VARCHAR(30) NOT NULL,
	L_Name VARCHAR(30) NOT NULL,
	Email VARCHAR(255) NOT NULL,
	Password VARCHAR(80) NOT NULL,
	created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	modified TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (User_ID),
	UNIQUE (Email)
)";

if ($conn->query($sql) === TRUE) {
  echo "Table Users created successfully".PHP_EOL;
} else {
  echo "Error creating table Users: " . $conn->error;
}

$conn->close();
?>
