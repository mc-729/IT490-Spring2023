<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require('safer_echo.php');
require('nav.php');




$answer = $_POST['ans'];  
if(isset($answer)){

    $client = new rabbitMQClient("RabbitMQConfig.ini", "APIServer");
    $request = '{"type":"SearchByName","operation": "s","ingredient": "'.$answer.'" }';
    $response = $client->send_request($request);
    print_r($response);

}

//if(!empty('itenName')){}
?>

</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

<h1>Product Search</h1>
<form action="apt_search.php" method="post">
  Vodka <input type="radio" name="ans" value="vodka" /><br />
  Whiskey <input type="radio" name="ans" value="whiskey"  /><br />
  Beer <input type="radio" name="ans" value="beer"  /><br />
  Wine <input type="radio" name="ans" value="wine"  /><br />
  <input type="submit" value="submit" />
</form>