<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('nav.php');

/*
if (isset($_POST["username"])) {
        $uname = $_POST["username"];
} else {
        $uname = "not recieved";
}
if (isset($_POST["email"])) {
        $email = $_POST["email"];
} else {
        $email = "not recieved";
}
if (isset($_POST["password"])) {
        $password = $_POST["password"];
} else {
        $password = "not recieved";
}


if (isset($_POST["fname"])) {
        $first_name = $_POST["fname"];
}



if (isset($_POST["lname"])) {
        $last_name = $_POST["lname"];
}

*/






$client = new rabbitMQClient("RabbitMQConfig.ini", "testServer");
if (isset($argv[1])) {
        $msg = $argv[1];
} else {
        $msg = "Update Profile Message";
}

$request = array();
$request['type'] = "Update";
$request['email'] = $email;
$request['username'] = $uname;
$request['curPW'] = $curPW;
$request['newPW'] = $newPW;
$request['conPW'] = $conPW;
$request['message'] = $msg;
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: " . PHP_EOL;
print_r($response);
echo "\n\n";
echo $argv[0] . " END" . PHP_EOL;

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