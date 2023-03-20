#!/usr/bin/php
<?php
//Load Composer's autoloader
require '../vendor/autoload.php';

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function sendEmail($email, $event, $days){
    $mail = new PHPMailer();
    // configure an SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'cocktailsearch@gmail.com';
    $mail->Password = 'tbhokigmqdobsbey';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    
    $mail->setFrom('cocktailsearch@gmail.com', 'Cocktail Search');
    $mail->addAddress($email, 'Me');
    $mail->Subject = 'Upcoming Event!';
    // Set HTML 
    $mail->isHTML(TRUE);
    $mail->Body = '<p>Hi there,</p><p>We are happy to inform you that {event} is coming up in {days} days.</p>';

    $mail->Body = str_replace('{event}', $event, $mail->Body);
    $mail->Body = str_replace('{days}', $days, $mail->Body);

    // send the message
    if(!$mail->send()){
        echo 'Message could not be sent.'.PHP_EOL;
        echo 'Mailer Error: ' . $mail->ErrorInfo.PHP_EOL;
    } else {
        echo 'Message has been sent'.PHP_EOL;
    }
}

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

// Sending request to update the timeleft on an event
$request1 = array();
$request1['type'] = "UpdateStartDates";
$dateResponse = $client->send_request($request1);

// Sending request to grab events that have less than 7 days left
$request2 = array();
$request2['type'] = "Events";
$request2['timeleft'] = "7";
$eventResponse = $client->send_request($request2);

print_r($eventResponse);
echo "\n\n";
echo "client received response: ".PHP_EOL;

if (isset($eventResponse)){
    foreach ($eventResponse as $value){
        // Sending request to get email addresses for each event
        $request3 = array();
        $request3['type'] = "Email";
        $request3['userid'] = $value['UID'];
        $emailResponse = $client->send_request($request3);
        $eventName = $value['name'];
        $daysLeft = $value['timeleft'];
        sendEmail($emailResponse, $eventName, $daysLeft);
    }
} else {
    print_r("No events");
    echo "\n\n";
}

echo $argv[0]." END".PHP_EOL;

?>