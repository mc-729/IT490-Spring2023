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
} else {
  echo "Connection Successful".PHP_EOL;	
}

// sql to create table
$sql = "Use IT490;";
    "CREATE TABLE IF NOT EXISTS `Users` (".
    "`id` INT NOT NULL AUTO_INCREMENT,".
    "`email` VARCHAR(100) NOT NULL,".
    "`password` VARCHAR(60) NOT NULL,".
    "`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,".
    "`modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,".
    " PRIMARY KEY (`id`),".
    " UNIQUE (`email`)".
");";

if ($conn->query($sql) === TRUE) {
  echo "Table Users created successfully";
} else {
  echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
