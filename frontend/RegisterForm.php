<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require('nav.php');


?>

</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>



<div class="container-fluid">
    <h1>Register</h1>
    <form action="RegisterForm.php" method="POST">
        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input class="form-control" type="email" id="email" name="email" required />
        </div>
        <div class="mb-3">
            <label class="form-label" for="username">Username</label>
            <input class="form-control" type="text" id="username" name="username" required maxlength="30" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="fname">First Name</label>
            <input class="form-control" type="text" id="fname" name="fname" required maxlength="30" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="lname">Last Name</label>
            <input class="form-control" type="text" id="lname" name="lname" required maxlength="30" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="pw">Password</label>
            <input class="form-control" type="password" id="pw" name="password" required minlength="8" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="confirm">Confirm</label>
            <input class="form-control" type="password" name="confirm" required minlength="8" />
        </div>
        <input type="submit" class="mt-3 btn btn-primary" value="Register" />
    </form>
</div>





<?php


if (isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["lname"]) && isset($_POST["lname"])) {
    $uname = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $first_name = $_POST["fname"];
    $last_name = $_POST["lname"];

    $client = new rabbitMQClient("RabbitMQConfig.ini", "testServer");
    $request = array();
    $request['type'] = "Register";
    $request['username'] = $uname;
    $request['password'] = $password;
    $request['email'] = $email;
    $request['firstName'] = $first_name;
    $request['lastName'] = $last_name;
    $response = $client->send_request($request);

    if($response){

        die(header("Location: /loginForm.php"));
    }

    else echo "something went wrong ";
}
?>