#!/usr/bin/php
<?php
$servername = "localhost";
$username = "testuser";
$password = "12345";
$dbname = "IT490";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$sql = "CREATE EVENT delete_old_cache
        ON SCHEDULE EVERY 1 HOUR
        DO
          DELETE FROM IT490.Cache
          WHERE timestamp < DATE_SUB(NOW(), INTERVAL 1 HOUR)";

if (mysqli_query($conn, $sql)) {
    echo "Event created successfully" . PHP_EOL;
} else {
    echo "Error creating event: " . mysqli_error($conn);
}

// Close the connection
mysqli_close($conn);