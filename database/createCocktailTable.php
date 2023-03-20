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
    User_ID INT NOT NULL,
    Recipe JSON,
    Ingredients JSON,
    PRIMARY KEY (User_ID),
    FOREIGN KEY (User_ID) REFERENCES IT490.Users(User_ID)
    )";

if ($conn->query($sql) === TRUE) {
  echo "Table Cocktails created successfully".PHP_EOL;
} else {
  echo "Error creating table Cocktails: " . $conn->error;
}

$conn->close();
?>