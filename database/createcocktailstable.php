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

// sql to create table Cocktails
$sql = "CREATE TABLE IT490.Cocktails(
    Cocktail_ID INT NOT NULL AUTO_INCREMENT,
    Name VARCHAR(255) NOT NULL,
    Glass VARCHAR(255) NOT NULL,
    Instruction TEXT,
    Picture VARCHAR(255),
    Ingredients JSON,
    PRIMARY KEY (Cocktail_ID)
    )";

if ($conn->query($sql) === TRUE) {
  echo "Table Cocktails created successfully".PHP_EOL;
} else {
  echo "Error creating table Cocktails: " . $conn->error;
}

$conn->close();
?>