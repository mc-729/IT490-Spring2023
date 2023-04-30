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
$sql = "CREATE TABLE IT490.UserRecipes(
    User_ID INT NOT NULL,
    id INT NOT NULL AUTO_INCREMENT,
    Drink_Name VARCHAR(60),
    Username VARCHAR(30) DEFAULT NULL,
    Recipe JSON DEFAULT NULL,
	created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (User_ID) REFERENCES IT490.Users(User_ID)
  

    )";

if ($conn->query($sql) === TRUE) {
  echo "Table UserMLC created successfully".PHP_EOL;
} else {
  echo "Error creating table UserMLC : " . $conn->error;
}

$conn->close();
?>