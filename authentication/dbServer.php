#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function loginAuth($username, $password)
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
		echo "Successfully Connected!" . PHP_EOL;
	}

	
// lookup username in database

	$sql = "SELECT * FROM IT490.Users WHERE Email = '$username'";
	$result = mysqli_query($conn, $sql);
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$count = mysqli_num_rows($result);


	if ($count != 0) {
		echo "User Found" . PHP_EOL;
		// Verify password

		$sql2 = "SELECT Password FROM IT490.Users WHERE Email = '$username'";
		$result2 = mysqli_query($conn, $sql2);
		$row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
		$hashedpass = $row2['Password'];

		echo $hashedpass . PHP_EOL;

		if (password_verify($password, $hashedpass)) {
			echo "Login Successful" . PHP_EOL;
			$resp = array(true, SessionGen($row['User_ID']), $row['User_ID']);
			return $resp;
		} else {
			echo "Login Failed" . PHP_EOL;
			$resp = array("login_status" => "false");
			return $resp;
		}
	} else {
		echo "Login Failed" . PHP_EOL;
		$resp = array("login_status" => "false");
		return $resp;
	}
} //End function loginAuth

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
		  return $conn;

}
function registrationInsert($username,$password,$email,$firstName,$lastName)
{// Check if Username/Email already exists for registering new account
	$servername = "localhost";
        $uname = "testuser";
	$pw = "12345";
 	$dbname = "IT490"; 
	$conn = new mysqli($servername, $uname, $pw, $dbname);

        $sqlRegi = "SELECT * FROM IT490.Users WHERE Email = '$email'";
        $resultRegi = mysqli_query($conn, $sqlRegi);
        $rowRegi = mysqli_fetch_array($resultRegi, MYSQLI_ASSOC);
        $countRegi = mysqli_num_rows($resultRegi);
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);

        if($countRegi == 1)// ==1 means found an already existing Username/Email in IT490.Users
        {
                echo "Username/Email already exists, please use a different one.".PHP_EOL;
                return false;
        }
        else //If Username/Email is not found in database/doesn't exist, do this
        {
                $sqlInsert = "INSERT into IT490.Users (Username,F_Name, L_Name, Email, Password)
                        VALUES ('$username','$firstName','$lastName','$email','$hashPassword')";
                
               
                
				if(mysqli_query($conn, $sqlInsert)){
					echo "New user registered, welcome. ";
					echo $sqlInsert;
				return true;}
        }
} // End funtion registrationInsert


function SessionGen($user_ID)
{

	$servername = "localhost";
	$uname = "testuser";
	$pw = "12345";
	$dbname = "IT490";

	// Create connection
	$conn = new mysqli($servername, $uname, $pw, $dbname);
	$check = "SELECT * from IT490.sessions where UID = $user_ID";
	$query = mysqli_query($conn, $check);
	$count = mysqli_num_rows($query);

	
		$sessionID = rand(1000, 99999999);
		$query2 = "INSERT into IT490.sessions(UID,SessionID)VALUES('$user_ID','$sessionID')";
		$result = mysqli_query($conn, $query2);
		return $sessionID;
	
}

function doValidate($sessionid)
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
		echo "Successfully Connected!" . PHP_EOL;
	}



	$sql = "SELECT * FROM IT490.sessions WHERE session_ID = '$sessionid'";
	$result = mysqli_query($conn, $sql);
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$count = mysqli_num_rows($result);

	if ($count != 0) {
		echo "Session is valid" . PHP_EOL;


		return true;
	} else {

		echo "Session is not valid" . PHP_EOL;
		return false;
	}
}


function requestProcessor($request)
{
	echo "received request" . PHP_EOL;
	var_dump($request);
	if (!isset($request['type'])) {
		return "ERROR: unsupported message type";
	}
	switch ($request['type']) {
		case "Login":
			return loginAuth($request['username'], $request['password']);
		case "Register":
      			return registrationInsert($request['username'],$request['password'],$request['email'],$request['firstName'],$request['lastName']);
		case "validate_session":
			return doValidate($request['sessionId']);
	}
	//$callLogin = array($callLogin => doLogin($username,$password)
	return array("returnCode" => '0', 'message' => "Server received the request and processed it.");
}

$server = new rabbitMQServer("RabbitMQConfig.ini", "testServer");

echo "Authentication Server BEGIN" . PHP_EOL;
$server->process_requests('requestProcessor');

echo "Authentication Server END" . PHP_EOL;
exit();
