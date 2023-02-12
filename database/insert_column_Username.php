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
ADD COLUMN Username VARCHAR(30) NOT NULL
AFTER User_ID;";

if ($conn->query($sql) === TRUE) {
        echo "Table Users altered successfully".PHP_EOL;
} else {
        echo "Error altering table Users: " .$conn->error;
}

$conn->close();

?>
