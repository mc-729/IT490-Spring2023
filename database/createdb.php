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
 echo "Connection Successful".PHP_EOL;
}

// Create database
$sql = "CREATE DATABASE IT490;"; 
"GRANT ALL PRIVILEGES ON IT490 to 'testuser'@'localhost';". 
" FLUSH PRIVILEGES;";

if ($conn->query($sql) === TRUE) {
  echo "Database created successfully".PHP_EOL;
} else {
  echo "Error creating database: " . $conn->error;
}

$conn->close();
?>

