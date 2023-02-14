#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


function doLog($msg,$type,$origin)
{
  $date = new DateTime('now');
  $date = $date->format("m/d/y h:i:s");
  echo "[$date] $origin [Type of msg: $type] [LOG: $msg]";
    
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['service'])
  {

    case "database":
      $originator = 'Database Server:';
      return doLog($request['message'], $request['type'], $originator);
    
    case "frontend":
      $originator = 'Frontend Server:';
      return doLog($request['message'], $request['type'], $originator);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","logServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

