#!/usr/bin/php
<?php
//Load Composer's autoloader
require '../vendor/autoload.php';

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function sendEmail($email){
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
    $mail->Body = '<html>Hi there, we are happy to <br>inform you of your upcoming event</br></html>';
    $mail->AltBody = 'Hi there, we are happy to inform you of your upcoming event.';

    // send the message
    if(!$mail->send()){
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message has been sent';
    }
}

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

$request1 = array();
$request1['type'] = "Events";
$request1['timeleft'] = "7";
$eventResponse = $client->send_request($request1);

print_r($eventResponse);
echo "\n\n";
echo "client received response: ".PHP_EOL;

if (isset($eventResponse)){
    foreach ($eventResponse as $value){
        $request2 = array();
        $request2['type'] = "Email";
        $request2['userid'] = $value['UID'];
        $emailResponse = $client->send_request($request2);
        sendEmail($emailResponse);
    }
} else {
    print_r("No events");
    echo "\n\n";
}

echo $argv[0]." END".PHP_EOL;

?>