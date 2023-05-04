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

$sql = "CREATE TABLE IT490.MFA(
	id INT NOT NULL AUTO_INCREMENT,
	UID int NOT NULL,
	MFA int NOT NULL,
	created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	UNIQUE (MFA),
	FOREIGN KEY (UID) REFERENCES Users(User_ID)
	)";

if ($conn->query($sql) === TRUE) {
	echo "Table MFA created successfully".PHP_EOL;
} else {
	echo "Error creating table MFA: " .$conn->error;
}

$conn->close();
?>
