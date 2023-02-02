#!/usr/bin/php
<?php
$servername = "localhost";
$username = "testuser";
$password = "12345";

// Create connection
$conn = new mysqli($servername, $username, $password);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
 echo "Connection Successful";
}

// Create database
$sql = "CREATE DATABASE IT490;
	grant all privileges on IT490.* to 'testuser'@'localhost';
	flush privileges;";
if ($conn->query($sql) === TRUE) {
  echo "Database created successfully";
} else {
  echo "Error creating database: " . $conn->error;
}

$conn->close();
?>
