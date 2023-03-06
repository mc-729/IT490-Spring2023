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

// sql to alter table Cocktails

$sql = "ALTER TABLE IT490.Cocktails
ADD Cocktail_Details JSON NOT NULL;";

if ($conn->query($sql) === TRUE) {
    echo "Table Cocktails altered successfully".PHP_EOL;
  } else {
    echo "Error altering table Cocktails: " . $conn->error;
  }
  
  $conn->close();
  ?>