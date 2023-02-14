#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","logServer");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  //$date = new DateTime('now');
  //$date = $date->format("m/d/y h:i:s");
  $msg = "This is the error message 9 this is to systemctl and logging to var logs\n";
}

$request = array();
$request['type'] = "error";
$request['service'] = "frontend";
$request['message'] = $msg;
//$response = $client->send_request($request);
$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;

