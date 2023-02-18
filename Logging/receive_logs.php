<?php

require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'test', 'test','testHost');
$channel = $connection->channel();

$channel->exchange_declare('eventFanout1', 'fanout', false, false, false);

list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);
$channel->queue_bind($queue_name,'eventFanout1');

echo "Starting Logs Server";
function doLog($msg,$type,$origin)
{
  $date = new DateTime('now');
  $date = $date->format("m/d/y h:i:s");
  echo "[$date] $origin [Type of msg: $type] [LOG: $msg]";
    
}

$callback = function ($msg) {
    
    switch ($msg['service']){
        case "database":
            $originator = 'Database Server:';
            return doLog($msg['message'], $msg['type'], $originator);

        case "database":
            $originator = 'Database Server:';
            return doLog($msg['message'], $msg['type'], $originator);
        
        }
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>