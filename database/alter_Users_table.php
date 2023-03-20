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

//sql to alter table Users

$sql = "ALTER TABLE IT490.Users
ADD COLUMN City VARCHAR(60) NOT NULL AFTER L_Name,
ADD COLUMN State VARCHAR(60) NOT NULL AFTER City;";

if ($conn->query($sql) === TRUE) {
        echo "Table Users altered successfully, added State and City column/field".PHP_EOL;
} else {
        echo "Error altering table Users: " .$conn->error;
}

$conn->close();

?>
