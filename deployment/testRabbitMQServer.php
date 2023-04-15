#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

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

