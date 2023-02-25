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

//sql to insert username

$sql = "UPDATE IT490.Users
SET Username = 'bsmith'
WHERE User_ID = 1;";
	
	
if ($conn->query($sql) === TRUE) {
        echo "Username for bsmith@gmail.com has been added".PHP_EOL;
} else {
        echo "Error adding Username: " .$conn->error;
}

$conn->close();

?>
