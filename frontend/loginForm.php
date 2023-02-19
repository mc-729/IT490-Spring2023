<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require('nav.php');
//require_once('../Logging/send_log.php');
?>
</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  <div class="container-fluid">
    <h1>Login</h1>
    <form  method="POST" action="loginForm.php">
        <div class="mb-3">
            <label class="form-label" for="email">Username/Email</label>
            <input class="form-control" type="text" id="email" name="email" required />
        </div>
        <div class="mb-3">
            <label class="form-label" for="pw">Password</label>
            <input class="form-control" type="password" id="pw" name="password" required minlength="4" />
        </div>
        <input type="submit" class="mt-3 btn btn-primary" value="Login" />
    </form>
</div>
<?php 
function sendLog ($message){

  $encodedMessage = json_encode($message);    

  $connection = new AMQPStreamConnection('localhost', 5672, 'test', 'test','testHost');
  $channel = $connection->channel();
  $channel->exchange_declare('eventFanout1', 'fanout', false, false, false);

  $msg = new AMQPMessage($encodedMessage);

  $channel->basic_publish($msg, 'eventFanout1');

  echo ' [x] Sent ', $encodedMessage, "\n";

  $channel->close();
  $connection->close();
}
if (isset($_POST["password"] ) and isset($_POST["email"]) ) {
    $uname = $_POST["email"];
    $password = $_POST["password"];
    $client = new rabbitMQClient("RabbitMQConfig.ini", "testServer");
    $request = array();
    $request['type'] = "Login";
    $request['username'] = $uname;
    $request['password'] = $password;
    $response = $client->send_request($request);
    // $response = $client->publish($request);

     //logging client
     $request = array();
     $request['type'] = "Login";
     $request['service'] = "Frontend";
     $request['message'] = "Sent Login request to DB server";
     sendLog($request);


  print_r($response);
   if($response[0]==1){
    echo "success";
   }
  }


?>

