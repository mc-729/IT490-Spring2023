#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function doLogin($username,$password)
{	
    // lookup username in database
	$sql = "SELECT * FROM IT490.Users WHERE username = '$username' AND password = '$password'";
	$result = mysqli_query($conn, $sql);
	$row = mysqli_fetch_array($reult, MYSQLI_ASSOC);
	$count = mysqli_num_rows($result);

	if($count != 0)
	{
		echo "Login Succesful".PHP_EOL;
		return true;
	}
	else
	{
		echo "Login Failed".PHP_EOL;
		return false;
	}
}//End function doLogin


//
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
    case "login":
      return doLogin($request['username'],$request['password']);
    case "validate_session":
      return doValidate($request['sessionId']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("RabbitMQConfig.ini","testServer");

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

echo "Authentication Server BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "Authentication Server END".PHP_EOL;
exit();


