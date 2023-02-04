code<html>
<h1>Register Page</h1>
</html>

<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

if (isset ($_POST["username"]))
{
        $uname = $_POST["username"];
}
else
{
        $uname="not recieved";
	
}
if (isset ($_POST["email"]))
{
        $email = $_POST["email"];
}
else
{
        $email ="not recieved";
}
if (isset ($_POST["password"]))
{
        $password = $_POST["password"];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $password = $hashedPassword;
}
else
{
	$password="not recieved";
}
if (isset ($_POST["confirm"]))
{
        $confPassword = $_POST["confirm"];
}
else
{$confPassword = "not recieved";
	
}

if ($password != $confPassword)
{
	echo "Passwords do not match".PHP_EOL;
	
}









$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "Default Register Message";
}

$request = array();
$request['type'] = "Register";
$request['username'] = $uname;
$request['password'] = $password;
$request['email'] = $email;
$request['message'] = $msg;
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";
echo $argv[0]." END".PHP_EOL;

/*if ($response["returnCode"] == '0')
{
        echo "Succesfully Register new Account, Redirecting to Login Page".PHP_EOL;
        header("refresh: 3, url=index.html");
}
else
{
        echo "Registering Account Failed, Please Try Again".PHP_EOL;
}
*/
?>