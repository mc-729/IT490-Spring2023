<?php
require('nav.php');
require('helper.php');
session_start();

?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>



<html>
<div class="card text-center">
    <div class="card-header">
        Featured
    </div>
    <div class="card-body">
        <h5 class="card-title">Welcome to the WIP Landing Page</h5>
        <?php if (isLoggedIn()) : ?>
            <p class="card-text">Hello you are logged in</p>
        <?php endif; ?>
        <?php if (!isLoggedIn())  : ?>
            <p class="card-text">Hello you are not logged in</p>
        <?php endif; ?>
        <a href="#" class="btn btn-primary">This button goes nowhere</a>
    </div>
    <div class="card-footer text-muted">
        big footer vibe
    </div>
</div>