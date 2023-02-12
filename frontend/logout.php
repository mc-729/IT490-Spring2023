<?php
session_start();

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
$client = new rabbitMQClient("RabbitMQConfig.ini", "testServer");
$request = array();
$request['type'] = "Logout";
$request['sessionID'] = $_SESSION['DB_ID'];

$response = $client->send_request($request);
if($response){

    session_unset();
    session_destroy();
    session_start();
    die(header("Location: /landingPage.php"));

}
?>

