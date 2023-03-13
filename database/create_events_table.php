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

$sql = "CREATE TABLE IT490.events(
	id INT NOT NULL AUTO_INCREMENT,
	UID INT NOT NULL,
	name TINYTEXT NOT NULL,
	description TEXT,
	image TEXT,
	link TEXT,
	startdate BIGINT NOT NULL,
	timeleft INT,
	created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	FOREIGN KEY (UID) REFERENCES Users(User_ID)
	)";

if ($conn->query($sql) === TRUE) {
	echo "Table sessions created successfully".PHP_EOL;
} else {
	echo "Error creating table sessions: " .$conn->error;
}

$conn->close();
?>
