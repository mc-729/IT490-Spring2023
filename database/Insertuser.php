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
$sql = "INSERT INTO IT490.Users (User_ID, F_Name, L_Name, Email, Password)
VALUES ('1', 'Bob', 'Smith', 'bsmith@gmail.com', 'password');";


if ($conn->query($sql) === TRUE) {
  echo "Users added successfully".PHP_EOL;
} else {
  echo "Error adding Users: " . $conn->error;
}

$conn->close();
?>
