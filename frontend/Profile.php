<?php
require('nav.php');
?>

</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

<form method="POST" onsubmit="return validate(this);">
<div class="container-fluid">
    <h1> Welcome to your Profile Page! </h1>
            <div class="col-md-6">
                <div class="profile-head">
                        <h5>
                            Username:
                        </h5>
                        <h6>
                            Email:
                        </h6>
                        <p class="proile-rating">Place Holder</p>
                        <div class="mb-3">
                <div class="form-check form-switch">
                    <input name="visibility" class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" <?php if ($public) echo "checked"; ?>>
                    <label class="form-check-label" for="flexSwitchCheckDefault">Make Profile Public</label>
                </div>
            </div>
                         <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="About Me-tab" data-toggle="tab" href="#AboutMe" role="tab" aria-controls="AboutMe" aria-selected="true">About Me</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="my liquor cabinet-tab" data-toggle="tab" href="#cabinet" role="tab" aria-controls="cabinet" aria-selected="false">My Liquor Cabinet</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="edit profile-tab" data-toggle="tab" href="#edit-profile" role="tab" aria-controls="edit-profile" aria-selected="false">Edit Profile</a>
                            </li>
                        </ul>
                </div>
            </div>
            <!--
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" type="email" name="email" id="email" value="<?php se($email); ?>" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="username">Username</label>
                <input class="form-control" type="text" name="username" id="username" value="<?php se($username); ?>" />
            </div>
-->
            <!-- DO NOT PRELOAD PASSWORD -->

            <!--
            <div class="mb-3">Password Reset</div>
            <div class="mb-3">
                <label class="form-label" for="cp">Current Password</label>
                <input class="form-control" type="password" name="currentPassword" id="cp" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="np">New Password</label>
                <input class="form-control" type="password" name="newPassword" id="np" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="conp">Confirm Password</label>
                <input class="form-control" type="password" name="confirmPassword" id="conp" />
            </div>
            <input type="submit" class="mt-3 btn btn-primary" value="Update Profile" name="save" />
        </form>
-->