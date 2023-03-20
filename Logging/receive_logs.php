<?php

require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'test', 'test','testHost');
$channel = $connection->channel();

$channel->exchange_declare('eventFanout', 'fanout', false, true, false);

list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);
$channel->queue_bind($queue_name,'eventFanout');

echo "Starting Logs Server".PHP_EOL;
function doLog($msg,$type,$origin)
{
  $date = new DateTime('now');
  $date = $date->format("m/d/y h:i:s");
  echo "[$date] $origin [Type of msg: $type] [LOG: $msg]".PHP_EOL;
    
}

$callback = function ($msg) {
    
    $body = $msg->getBody();
    $decodedMsg = json_decode($body, true);

    switch ($decodedMsg['service']){
        case "database":
            $originator = 'Database Server:';
            return doLog($decodedMsg['message'], $decodedMsg['type'], $originator,);

        case "frontend":
            $originator = 'Frontend Server:';
            return doLog($decodedMsg['message'], $decodedMsg['type'], $originator,);

        case "API":
            $originator = 'API Server:';
            return doLog($decodedMsg['message'], $decodedMsg['type'], $originator,);
        
        }
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>