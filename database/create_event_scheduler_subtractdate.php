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


$sql = "CREATE EVENT subtract_dates
        ON SCHEDULE EVERY 1 DAY
        STARTS CURRENT_TIMESTAMP + INTERVAL 1 DAY
        DO
            BEGIN
                DECALRE i INT DEFAULT 0;
                DECALRE num_rows INT;
                SELECT COUNT(*) INTO num_rows FROM events;

                WHILE i < num_rows DO
                    SELECT id AS eventid, DATEDIFF(startdate, CURDATE()) AS days_remaining;
                    UPDATE events SET timeleft = days_remaining WHERE id = eventid;
                    SET i = i + 1;
                END WHILE;
            END";

if (mysqli_query($conn, $sql)) {
    echo "Event created successfully" . PHP_EOL;
} else {
    echo "Error creating event: " . mysqli_error($conn);
}

// Close the connection
mysqli_close($conn);