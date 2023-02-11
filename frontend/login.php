<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('nav.php');
require_once('safer_echo.php');





if (isset($_POST["password"] ) and isset($_POST["email"]) ) {

  $uname = $_POST["email"];
  $password = $_POST["password"];
  $client = new rabbitMQClient("RabbitMQConfig.ini", "testServer");
  $client = new rabbitMQClient("RabbitMQConfig.ini","testServer");

  if (isset($argv[1]))
  {
    $msg = $argv[1];
  }
  else
  {
    $msg = "test message";
  }

  $request = array();
  $request['type'] = "Login";
  $request['username'] = $uname;
  $request['password'] = $password;
  $request['message'] = $msg;
  $response = $client->send_request($request);
  // $response = $client->publish($request);

  echo "client received response: ".PHP_EOL;
  print_r($response);
  echo "\n\n";
  
  echo $argv[0]." END".PHP_EOL;
 
}
?>
</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

