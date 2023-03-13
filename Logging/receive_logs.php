<?php

require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

<<<<<<< HEAD
$connection = new AMQPStreamConnection('localhost', 5672, 'test', 'test','testHost');
=======
$connection = new AMQPStreamConnection('192.168.191.15', 5672, 'test', 'test','testHost');
>>>>>>> 6dd523f202564c314e774824c289fc0b85f0e660
$channel = $connection->channel();

$channel->exchange_declare('eventFanout1', 'fanout', false, false, false);

list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);
$channel->queue_bind($queue_name,'eventFanout1');

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
        
        }
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>