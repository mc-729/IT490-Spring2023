#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


function dbConnection()
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

}//End function dbConnection


function loginAuth($username,$password)
{
	$servername = "localhost";
	$uname = "testuser";
	$pw = "12345";
	$dbname = "IT490";

// lookup username in database
	$sql = "SELECT * FROM IT490.Users WHERE Email = '$username'";
	$result = mysqli_query($conn, $sql);
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$count = mysqli_num_rows($result);
	
	if($count != 0)
	{
		echo "User Found".PHP_EOL;
// Verify password
		$sql2 = "SELECT Password FROM IT490.Users WHERE Email = '$username'";
		$result2 = mysqli_query($conn, $sql2);
		$row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
		$hashedpass = $row2['Password'];
		echo $hashedpass.PHP_EOL;
 
		if(password_verify($password, $hashedpass))
		{
			echo "Login Successful".PHP_EOL;
			$resp = array("login_status" => "true");
			return $resp;
		} else {
			echo "Login Failed".PHP_EOL;
        	        $resp = array("login_status" => "false");
                	return $resp;
		}
	} else {
		echo "Login Failed".PHP_EOL;
		$resp = array("login_status" => "false");
                return $resp;

	}
}//End function loginAuth


//Start function registrationInsert
/*function registrationCheck
{// Check if Username/Email already exists for registering new account
        $sqlRegi = "SELECT * FROM IT490.Users WHERE Email = '$username'";
        $resultRegi = mysqli_query($conn, $sqlRegi);
        $rowRegi = mysqli_fetch_array($resultRegi, MYSQLI_ASSOC);
        $countRegi = mysqli_num_rows($resultRegi);

        if($countRegi != 0)
        {
                echo "Username/Email already exists, please use a different one.".PHP_EOL;


}
 */


//Start function requestProcessor
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
    
    //WIP
    /*
   
    case "Registration":
      return registrationInsert($request['']);
   
    */
    
    case "validate_session":
      return doValidate($request['sessionId']);
  }
  //$callLogin = array($callLogin => doLogin($username,$password)
  return array("returnCode" => '0', 'message'=>"Server received the request and processed it.");
}

$server = new rabbitMQServer("RabbitMQConfig.ini","testServer");
dbConnection();
echo "Authentication Server BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "Authentication Server END".PHP_EOL;
exit();


