<?php

$BASE_PATH = '/Project';

//require db.php
require_once(__DIR__ . "/db.php");

//Flash Message Helpers
require(__DIR__ . "/flash_messages.php");

//require safer_echo.php
require(__DIR__ . "/safer_echo.php");

//filter helpers
require(__DIR__ . "/sanitizers.php");

//User helpers
require(__DIR__ . "/user_helpers.php");

//duplicate email/username
require(__DIR__ . "/duplicate_user_details.php");

//reset session
require(__DIR__ . "/reset_session.php");

//get URL
require(__DIR__ . "/get_url.php");

//Score functions
require(__DIR__ . "/score_helpers.php");

//Points functions
require(__DIR__ . "/points_helpers.php");

//Competition functions
require(__DIR__ . "/competition_helpers.php");

//Paginate
require(__DIR__ . "/paginate.php");

//Redirect
require(__DIR__ . "/redirect.php");

//get columns
require(__DIR__ . "/get_columns.php");

//save data
require(__DIR__ . "/save_data.php");

?>