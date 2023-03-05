#!/usr/bin/php

<?php

// before running this script cd into /etc/mysql/my.cnf 
// add [mysqld]
// event_scheduler=on
// save file and quit

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

// creating the event

$sql = "CREATE EVENT delete_old_cache 
        ON SCHEDULE EVERY 1 HOUR 
        DO DELETE from IT490.Cache WHERE timestamp < DATE_SUB(NOW(), INTERVAL 1 Hour)";

if(mysqli_query($conn,$sql)){
    echo "Event was created successfully";
} else{
    echo "Error creating Error: " . mysqli_error($conn);
}

$conn->close();
?>