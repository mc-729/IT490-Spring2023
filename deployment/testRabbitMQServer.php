#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function dbConnection()
{
    $servername = 'localhost';
    $uname = 'testuser';
    $pw = '12345';
    $dbname = 'deployment';
    
    // Create connection
    $conn = new mysqli($servername, $uname, $pw, $dbname);

    // Check connection
    if ($conn->connect_error) {
        echo 'Failed to connect to MySQL: ' . $conn->connect_error;
        $request = [];
        $request['type'] = 'error';
        $request['service'] = 'database';
        $request['message'] = 'DB CONNECTION FAILED';
        //$conn->connect_error;
        //sendLog($request);
        exit();
    } else {
        $request = [];
        $request['type'] = 'error';
        $request['service'] = 'database';
        $request['message'] = 'DB CONNECTION SUCCESSFUL';
        //sendLog($request);
        echo 'Successfully Connected!' . PHP_EOL;
    }
    return $conn;
} // End dbConnection
function doLogin($username,$password)
{
    // lookup username in databas
    // check password
    return true;
    //return false if not valid
}
function deployment($senderUser, $senderHost, $sourceDir, $zipName, $receiverUser, $receiverHost, $receiverFolder, $receiverDir)
{
    echo "we made it here";
    $bashScript = <<<BASH
#!/bin/bash

# Sender VM variables
SENDER_USER="$senderUser"
SENDER_HOST="$senderHost"
SOURCE_DIR="$sourceDir"
ZIP_NAME="$zipName"

# Receiver VM variables
RECEIVER_USER="$receiverUser"
RECEIVER_HOST="$receiverHost"
RECEIVER_FOLDER="$receiverFolder"
RECEIVER_DIR="$receiverDir"

# Control VM folder to save zip
CONTROL_VM_ZIP_DIR="./saved_zips"

# Create the folder to save zip on control VM if it doesn't exist
mkdir -p \$CONTROL_VM_ZIP_DIR

# SSH into sender VM, zip files, transfer files to receiver VM, save files on control VM, and unzip files on the receiver VM
echo "Running commands on sender VM and transferring files to receiver VM and control VM..."
ssh -t \$SENDER_USER@\$SENDER_HOST << EOF
  # Zip files
  echo "Zipping files..."
  cd \$SOURCE_DIR && zip -r \$ZIP_NAME *

  # Transfer zipped file using SSH keys to the receiver VM
  echo "Transferring files to receiver VM..."
  scp \$SOURCE_DIR/\$ZIP_NAME \$RECEIVER_USER@\$RECEIVER_HOST:\$RECEIVER_DIR

  # Transfer zipped file using SSH keys to the control VM
  echo "Transferring files to control VM..."
  scp \$SOURCE_DIR/\$ZIP_NAME \$USER@localhost:\$CONTROL_VM_ZIP_DIR

  # Unzip files on remote server
  echo "Unzipping files on remote server..."
  ssh \$RECEIVER_USER@\$RECEIVER_HOST "cd \$RECEIVER_DIR && unzip -o \$ZIP_NAME -d \$RECEIVER_FOLDER && rm \$ZIP_NAME"

  # Cleanup: Remove zip file on sender VM
  rm \$SOURCE_DIR/\$ZIP_NAME

  echo "File transfer complete."
EOF
BASH;
$scriptFilename = "transfer_script.sh";
file_put_contents($scriptFilename, $bashScript);

// Make the script executable
chmod($scriptFilename, 0755);

// Execute the generated script
$output = shell_exec("./$scriptFilename");
echo $output;
    return true;
}

function getLastVersion($packageName){
  $conn = dbConnection();
  $version = 1.0;
  // lookup package in database

  $sql = "SELECT * FROM deployment.packagelist WHERE name = '$packageName'";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $count = mysqli_num_rows($result);

  if ($count != 0) {
    echo 'Package Found' . PHP_EOL;

    // Verify password
    $sql2 = "SELECT * FROM deployment.packagelist WHERE name = '$packageName' ORDER BY version DESC LIMIT 1";
    $result2 = mysqli_query($conn, $sql2);
    $row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
    $version = $row2['version'];
    return $version;
  } else {
    echo 'Package Not Found' . PHP_EOL;
    return $version;
  }
  
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "login":
      return doLogin($request['username'],$request['password']);

    case"deploy":
      return deployment($request['sendUser']   ,$request['senderHost'] ,$request['sourceDir']   ,$request['zipName'],$request['receiverUser']   ,$request['receiverHost'],$request['receiverFolder']   ,$request['receiverDir']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

