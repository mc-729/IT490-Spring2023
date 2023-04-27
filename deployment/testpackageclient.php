<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

$senderUser = "jonathan";
$senderHost = "192.168.191.143";
$sourceDir = "/home/jonathan/git/";
$zipName = "test.txt";
$receiverUser = "it490";
$receiverHost = "192.168.191.12";
$receiverFolder = "authentication";
$receiverDir = "/home/it490/git/";

$request = array();
$request['type'] = "deploy";
$request['sendUser'] =$senderUser ;
$request['senderHost'] =  $senderHost;
$request['sourceDir'] =$sourceDir;
$request['zipName'] = $zipName;
$request['receiverUser'] =$receiverUser;
$request['receiverHost'] = $receiverHost;
$request['receiverFolder'] = $receiverFolder;
$request['receiverDir'] = $receiverDir;
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;