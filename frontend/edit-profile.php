<?php
require('nav.php');
require('helper.php');
require('safer_echo.php');
session_start();

?>

</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

<form method="POST" onsubmit="return validate(this);">
<div class="container-fluid">
    <h1> Welcome </h1>
            <div class="col-md-6">
                <div class="profile-head">
                        <h5>
                            Username: <?php se($_SESSION['Username']); ?>
                        </h5>
                        <h6>
                            Email: <?php se($_SESSION['Email']); ?>
                        </h6>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input name="visibility" class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" <?php if ($public) echo "checked"; ?>>
                            <label class="form-check-label" for="flexSwitchCheckDefault">Make Profile Public</label>
                        </div>
                    </div>
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="About Me-tab" data-toggle="tab" href="/AboutMe.php" role="tab" aria-controls="AboutMe" aria-selected="false">About Me</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="my liquor cabinet-tab" data-toggle="tab" href="/cabinet.php" role="tab" aria-controls="cabinet" aria-selected="false">My Liquor Cabinet</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="edit profile-tab" data-toggle="tab" href="/edit-profile.php" role="tab" aria-controls="edit-profile" aria-selected="true">Edit Profile</a>
                            </li>
                        </ul>
                </div>
            </div>
        
            <!-- DO NOT PRELOAD PASSWORD -->

            <div class="mb-3"><h3>Password Reset</h3></div>
            <form action="updateProfile.php" method="POST">
            <div class="mb-3">
                <label class="form-label" for="cp">Current Password</label>
                <input class="form-control" type="curPW" name="curPW" id="curPW" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="np">New Password</label>
                <input class="form-control" type="newPW" name="newPW" id="newPW" required minlength="8" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="conPW">Confirm New Password</label>
                <input class="form-control" type="conPW" name="conPW" id="conPW" required minlength="8" />
            </div>
            <input type="submit" class="mt-3 btn btn-primary" value="Update Password" name="save" />
            </form>
</form>


<?php

if (isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["oldPW"]) && isset($_POST["newPW"]) && isset($_POST["conPW"])) {
    $uname = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];


$client = new rabbitMQClient("RabbitMQConfig.ini", "testServer");
$request = array();
$request['type'] = "Update";
$request['email'] = $email;
$request['username'] = $uname;
$request['oldPW'] = $oldPW;
$request['newPW'] = $newPW;
$request['conPW'] = $conPW;
$response = $client->send_request($request);

if($response){

    die(header("Location: /Profile.php"));
} else echo "Updating Password Failed";

}

?>