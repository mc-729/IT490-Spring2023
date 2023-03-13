<?php

require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

function sendLog ($message){

    $encodedMessage = json_encode($message);    

<<<<<<< HEAD
    $connection = new AMQPStreamConnection('localhost', 5672, 'test', 'test','testHost');
=======
    $connection = new AMQPStreamConnection('192.168.191.15', 5672, 'test', 'test','testHost');
>>>>>>> 6dd523f202564c314e774824c289fc0b85f0e660
    $channel = $connection->channel();
    $channel->exchange_declare('eventFanout1', 'fanout', false, false, false);

    /*
    $data = implode(' ', array_slice($argv, 1));
    if (empty($data)) {
        $data = 'Hello World';
    }

    $msg = new AMQPMessage($data); 
    */
    $msg = new AMQPMessage($encodedMessage);

    $channel->basic_publish($msg, 'eventFanout1');

    echo ' [x] Sent ', $encodedMessage, "\n";

    $channel->close();
    $connection->close();
}

$request = array();
$request['type'] = "error";
$request['service'] = "database";
$request['message'] = "Test Message";

sendLog($request);

?>