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

// sql to create table UserMLC
$sql = "CREATE TABLE IT490.UserMLC(
    User_ID INT NOT NULL,
    Ing_Name VARCHAR(60),
    Amount FLOAT(5,2),
    Measurement_Type VARCHAR(60),
    PRIMARY KEY (User_ID, Ing_Name),
    FOREIGN KEY (User_ID) REFERENCES IT490.Users(User_ID)
    )";

if ($conn->query($sql) === TRUE) {
  echo "Table UserMLC created successfully".PHP_EOL;
} else {
  echo "Error creating table UserMLC : " . $conn->error;
}

$conn->close();
?>