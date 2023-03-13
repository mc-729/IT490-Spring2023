<?php

require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

function sendLog ($message){

    $encodedMessage = json_encode($message);    

    $connection = new AMQPStreamConnection('localhost', 5672, 'test', 'test','testHost');

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

?>