<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('nav.php');


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
if (isset($_POST["oldPW"])) {
        $password = $_POST["oldPW"];
} else {
        $password = "not recieved";
}
if (isset($_POST["newPW"])) {
        $password = $_POST["newPW"];
} else {
        $password = "not recieved";
}
if (isset($_POST["conPW"])) {
        $password = $_POST["conPW"];
} else {
        $password = "not recieved";
}

$client = new rabbitMQClient("RabbitMQConfig.ini", "testServer");
$clientLog = new rabbitMQClient("testRabbitMQ.ini","logServer");
if (isset($argv[1])) {
        $msg = $argv[1];
} else {
        $msg = "Update Profile Message";
}

$request = array();
$request['type'] = "Update";
$request['email'] = $email;
$request['username'] = $uname;
$request['oldPW'] = $oldPW;
$request['newPW'] = $newPW;
$request['conPW'] = $conPW;
$request['message'] = $msg;
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: " . PHP_EOL;
print_r($response);
echo "\n\n";
echo $argv[0] . " END" . PHP_EOL;

$msg = "Sent Update request to database";
$request = array();
$request['type'] = "Update";
$request['service'] = "frontend";
$request['message'] = $msg;
$response = $clientLog->send_request($request);


?>