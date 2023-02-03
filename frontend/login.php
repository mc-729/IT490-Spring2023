<?php


if (!isset($_POST))
{
	$msg = "NO POST MESSAGE SET";
	echo json_encode($msg);
	exit(0);
}
$request = $_POST;
$data = array();
echo $response;
switch ($request["submit"])
{
	case "login&uname":
		$data = array('email' => $_POST['email'],
					  'password' => $_POST['pw'],				  
	);
	echo "Your login data is ".$data. "<br>";
	break;
}
echo json_encode($response);
exit(0);

?>
