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
}function sendToControlVM($senderUser, $senderHost, $sourceDir, $zipName, $localPath)
{$receiverDir = "/home/it490/git/IT490-Spring2023/";
    $bashScript = <<<BASH
#!/bin/bash

# Configuration
REMOTE_USER="$senderUser"
REMOTE_PATH="$sourceDir"
LOCAL_PATH="$localPath"
ZIP_NAME="$zipName"
REMOTE_HOST="$senderHost"
# Zip files on the remote server
ssh "\${REMOTE_USER}@\${REMOTE_HOST}" "cd \${REMOTE_PATH} && zip -r \${ZIP_NAME} ."

# Rsync the zipped file to the local machine
rsync -avzP --remove-source-files "\${REMOTE_USER}@\${REMOTE_HOST}:\${REMOTE_PATH}/\${ZIP_NAME}" "\${LOCAL_PATH}/\${ZIP_NAME}"


BASH;
    $scriptFilename = "send_to_control_vm_script.sh";
    file_put_contents($scriptFilename, $bashScript);

    // Make the script executable
    chmod($scriptFilename, 0755);

    // Execute the generated script
    $output = shell_exec("./$scriptFilename");
    echo $output;
    return true;
}


function sendToReceiverVM($localPath, $zipName, $receiverUser, $receiverHost, $receiverFolder, $receiverDir)
{
  $bashScript = <<<BASH
  #!/bin/bash
  
  # Configuration
  LOCAL_PATH="$localPath"
  ZIP_NAME="$zipName"
  RECEIVER_USER="$receiverUser"
  RECEIVER_HOST="$receiverHost"
  RECEIVER_FOLDER="$receiverFolder"
  RECEIVER_DIR="$receiverDir"
  
  # Transfer zipped file using Rsync to the receiver VM
  echo "Transferring files to receiver VM..."
  rsync -avzP "\${LOCAL_PATH}/\${ZIP_NAME}" "\${RECEIVER_USER}@\${RECEIVER_HOST}:\${RECEIVER_DIR}"
  
  # Unzip files on remote server
  echo "Unzipping files on remote server..."
  ssh "\${RECEIVER_USER}@\${RECEIVER_HOST}" "cd \${RECEIVER_DIR} && unzip -o \${ZIP_NAME} -d \${RECEIVER_FOLDER} && rm \${ZIP_NAME}"
  
  echo "File transfer and unzip complete."
  
  BASH;
    $scriptFilename = "send_to_receiver_vm_script.sh";
    file_put_contents($scriptFilename, $bashScript);

    // Make the script executable
    chmod($scriptFilename, 0755);

    // Execute the generated script
    $output = shell_exec("./$scriptFilename");
    echo $output;
}
function deployment($senderUser, $senderHost, $sourceDir, $zipName, $receiverUser, $receiverHost, $receiverFolder, $receiverDir)
{
    // Control VM folder to save zip
    $controlVmZipDir = "./saved_zips";
    $LOCAL_PATH="/home/it490/git/IT490-Spring2023/deployment/package_repo";
  
    $ZIP_NAME="archive.zip"; // note this will be set with version control db
    // Call the sendToControlVM function to create a zip and send it to the control VM
    if(sendToControlVM($senderUser, $senderHost, $sourceDir, $zipName, $LOCAL_PATH))

    // Call the sendToReceiverVM function to pull the zip from the control VM folder and send it to the receiving VM, then unpack it
   {sendToReceiverVM($LOCAL_PATH,$zipName ,$receiverUser,$receiverHost, $receiverFolder, $receiverDir );
  
  
  }

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

