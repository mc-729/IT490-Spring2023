#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function loginAuth($username,$password)
{
	$servername = "localhost";
	$uname = "testuser";
	$pw = "12345";
	$dbname = "IT490";

// Create connection
	$conn = new mysqli($servername, $uname, $pw, $dbname);

// Check connection
	if ($conn->connect_error) {
  		die("Connection failed: " . $conn->connect_error);
	} else {
        echo "Successfully Connected!".PHP_EOL;
	}

// Hashing password
	$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
	echo $hashedPassword.PHP_EOL;

	
// lookup username in database
	$sql = "SELECT * FROM IT490.Users WHERE Email = '$username' and Password = '$hashedPassword'";
	$result = mysqli_query($conn, $sql);
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$count = mysqli_num_rows($result);
	
	if($count != 0)
	{
		echo "Login Succesful".PHP_EOL;
		$resp = array("login_status" => "true");
		return $resp;
	}
	/*elseif($count == 1)
	{
		echo "Login Success".PHP_EOL;
		return "true";
	}
	else{
		break;
	}
	 */
	else
	{
		echo "Login Failed".PHP_EOL;
		$resp = array("login_status" => "false");
                return $resp;

	}
}//End function loginAuth


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
    case "Login":
      return loginAuth($request['username'],$request['password']);
    case "validate_session":
      return doValidate($request['sessionId']);
  }
  //$callLogin = array($callLogin => doLogin($username,$password)
  return array("returnCode" => '0', 'message'=>"Server received the request and processed it.");
}

$server = new rabbitMQServer("RabbitMQConfig.ini","testServer");

echo "Authentication Server BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "Authentication Server END".PHP_EOL;
exit();


