
<?php

function isLoggedIn()
{
        if(isset($_SESSION['DB_ID']))
        {return true;}
        else return false;
}
?>