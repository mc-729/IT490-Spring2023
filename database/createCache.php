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

//sql to create sessions table

$sql = "CREATE TABLE IT490.Cache(
	SearchKey VARCHAR(255) NOT NULL,
	Results JSON DEFAULT NULL,
	created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (SearchKey)
	
	)";

if ($conn->query($sql) === TRUE) {
	echo "Table Cache created successfully".PHP_EOL;
} else {
	echo "Error creating table cache: " .$conn->error;
}

$conn->close();
?>
