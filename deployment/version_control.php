#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function dbConnection()
{
    $servername = 'localhost';
    $uname = 'testuser';
    $pw = '12345';
    $dbname = 'deployment';
    
    // Create connection
    $conn = new mysqli($servername, $uname, $pw, $dbname);

    // Check connection
    if ($conn->connect_error) {
        echo 'Failed to connect to MySQL: ' . $conn->connect_error;
        $request = [];
        $request['type'] = 'error';
        $request['service'] = 'database';
        $request['message'] = 'DB CONNECTION FAILED';
        //$conn->connect_error;
        //sendLog($request);
        exit();
    } else {
        $request = [];
        $request['type'] = 'error';
        $request['service'] = 'database';
        $request['message'] = 'DB CONNECTION SUCCESSFUL';
        //sendLog($request);
        echo 'Successfully Connected!' . PHP_EOL;
    }
    return $conn;
} // End dbConnection
function doLogin($username,$password)
{
    // lookup username in databas
    // check password
    return true;
    //return false if not valid
}

function getStableVersion($packageName){
   $conn = dbConnection();
   $version = 1.0;
  // lookup package in database

  $sql = "SELECT * FROM deployment.packagelist WHERE name = '$packageName'";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $count = mysqli_num_rows($result);

  if ($count != 0) {
    echo 'Package Found' . PHP_EOL;

    
    $sql2 = "SELECT * FROM deployment.packagelist WHERE name = '$packageName' AND status = 1 ORDER BY version DESC LIMIT 1";
    $result2 = mysqli_query($conn, $sql2);
    $row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
    $version = $row2['version'];
    return $version;
  } else {
    echo 'Package Not Found' . PHP_EOL;
    return $version;
  } 
}

function addStatus($packageName, $status){
    // 0 for fail and 1 for success
    $conn = dbConnection();

    $sql = "INSERT INTO deployment.packagelist (status) VALUES ('$status') 
            WHERE name = '$packageName'";
    if (mysqli_query($conn, $sql)){
        echo 'Package status insert in db'. PHP_EOL;
        echo $sql;
      } else {
        echo "Package status insert failed". PHP_EOL;
      }
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case"verify":
        return getStableVersion($request['zipName']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>