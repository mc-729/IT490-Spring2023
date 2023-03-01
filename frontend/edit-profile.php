<?php
require_once 'path.inc';
require_once 'get_host_info.inc';
require_once 'rabbitMQLib.inc';
require 'nav.php';
require 'helper.inc';
require 'safer_echo.php';
session_start();
?>
   <title>Edit Profile</title>
   <head>
</script>
 <!-- Bootstrap CSS -->
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-ZViMeuL6X9eh6yWim0G1OASOgzfKgjT7rbQ/kvl48OMI7MhO95/j9gmKtssYP/Bt" crossorigin="anonymous">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-QWo7pfQgLbP5nf7jbVvU7Z6XpoqVuKTfP2v+5f5WQL2MlrxMop6k1p1a6lgPLvoG" crossorigin="anonymous"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-Hb4AETyn8m3m3l4/wmQoZktzIVZSpfF9KjSWhSdL0xkwOweyhoKj3q4t2wzkyfzm" crossorigin="anonymous"></script>

<head>
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
                        <input name="visibility" class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" <?php if (
                            $public
                        ) {
                            echo 'checked';
                        } ?>>
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


        <form action="edit-profile.php" method="POST">
            <div class="mb-3">
                <label class="form-label" for="email" v>Email</label>
                <input class="form-control" value="<?php se(
                    $_SESSION['Email']
                ); ?>" type="email" id="email" name="email" required />
            </div>
            <div class="mb-3">
                <label class="form-label" for="username">Username</label>
                <input class="form-control" type="text" id="username" name="username" value="<?php se(
                    $_SESSION['Username']
                ); ?>" required maxlength="30" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="fname">First Name</label>
                <input class="form-control" type="text" id="fname" name="fname" value="<?php se(
                    $_SESSION['FirstName']
                ); ?>" required maxlength="30" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="lname">Last Name</label>
                <input class="form-control" type="text" id="lname" name="lname" value="<?php se(
                    $_SESSION['LastName']
                ); ?>" required maxlength="30" />
            </div>
            <div class="mb-3">
                <h3>Password Reset</h3>
            </div>
            <div class="mb-3">
                <label class="form-label" for="cp">Current Password</label>
                <input class="form-control" type="curPW" name="curPW" id="curPW" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="np">New Password</label>
                <input class="form-control" type="newPW" name="newPW" id="newPW" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="conPW">Confirm New Password</label>
                <input class="form-control" type="conPW" name="conPW" id="conPW" />
            </div>
            <input type="submit" class="mt-3 btn btn-primary" value="Update user details " name="save" />
        </form>
</form>


<?php
$uname;
$email;
$lastName;
$Firstname;
$oldPW;
$newPW;
$conPW;
if ($_POST['username'] != $_SESSION['Username']) {
    $uname = $_POST['username'];
}

$email = $_POST['email'];
$password = $_POST['password'];
$hasError = false;
if (
    (isset($_POST['curPW']) && isset($_POST['conPW'])) ||
    isset($_POST['newPW'])
) {
    if ($conPW != $newPW) {
        $hasError = true;
    } else {
        $oldPW = $_POST['curPW'];
        $newPW = $_POST['newPW'];
        $conPW == $_POST['conPW'];
    }
}

if ($_POST['username'] != $_SESSION['Username']) {
    is_valid_name($_POST['username'])
        ? ($uname = $_POST['username'])
        : ($hasError = true);
}
if ($_POST['fname'] != $_SESSION['FirstName']) {
    is_valid_name($_POST['fname'])
        ? ($Firstname = $_POST['fname'])
        : ($hasError = true);
}
if ($_POST['lname'] != $_SESSION['LastName']) {
    is_valid_name($_POST['lname'])
        ? ($lastName = $_POST['lname'])
        : ($hasError = true);
}
if ($_POST['email'] != $_SESSION['Email']) {
    is_valid_email($_POST['email'])
        ? ($email = sanitize_email($_POST['email']))
        : ($hasError = true);
}
if ($hasError) {
    display_error_modal('Oops, something went wrong!');
}
if (
    (!$hasError && !empty($email)) ||
    !empty($uname) ||
    !empty($Firstname) ||
    !empty($Lastname) ||
    !empty($newPW)
) {
    $client = new rabbitMQClient('RabbitMQConfig.ini', 'testServer');
    $request = [];
    $request['type'] = 'Update';
    $request['sessionID'] = $_SESSION['DB_ID'];
    $request['email'] = $email;
    $request['username'] = $uname;
    $request['firstName'] = $Firstname;
    $request['lastName'] = $lastName;
    $request['oldPW'] = $oldPW;
    if ($conPW == $newPW && isset($oldPW)) {
        $request['newPW'] = $newPW;
    }
    $response = $client->send_request($request);

    if ($response) {
        die(header('Location: /Profile.php'));
    } elseif (!$response) {
        die(header('Location: /logout.php'));
    }
}
?>



<?php function display_error_modal($error_message)
{
    ?>
    <!-- Error modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><?php echo $error_message; ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to display the modal -->
    <script>
        $(document).ready(function() {
            $('#errorModal').modal('show');
        });
    </script>
    <?php
}
?>
