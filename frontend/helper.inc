<?php

function isLoggedIn()
{
        if(isset($_SESSION['DB_ID']))
        {return true;}
        else return false;
}
function passwordConfirm($val1,$val2){

        if($val1==$val2)
        return true;
        else 
        return false;
}


function is_valid_name($val)
{
    return preg_match('/^[a-z0-9_-]{3,16}$/', $val);
}



 function sanitize_email($email = "")
{
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}
function is_valid_email($email = "")
{
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
}
?>