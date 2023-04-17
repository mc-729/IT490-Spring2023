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

function sendToControlVM($senderUser, $senderHost, $sourceDir, $zipName, $localPath)
{

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
function deployment($clusterName, $listnerName)

{

/**

This function deploys an application package between two virtual machines (VMs). It takes the following parameters:
  1.$senderUser: The username for the sending VM (to be hardcoded in the next refactor)
  2.$senderHost: The IP address or hostname of the sending VM
  3.$sourceDir: The directory path on the sending VM containing the files to be packaged.(to be hardcoded in the next refactor)
  4.$zipName: The name of the zip file to be created for the application package  (this will be retrieved from versiom control database)
  5.$receiverUser: The username for the receiving VM (to be hardcoded in the next refactor)
  6.$receiverHost: The IP address or hostname of the receiving VM
  7.$receiverFolder: The directory path on the receiving VM where the package will be stored.(to be hardcoded in the next refactor)
  8.$receiverDir: The directory path on the receiving VM where the package will be extracted.(to be hardcoded in the next refactor)

The function performs the following steps:
  1.Creates a zip archive of the specified source directory on the sending VM
  2.Sends the zip archive to a control VM, which acts as an intermediary
  3.Retrieves the zip archive from the control VM
  4.Sends the zip archive to the specified folder on the receiving VM
  5.Extracts the zip archive to the specified directory on the receiving VM
  6.In the final version of this function, the user will only need to provide the cluster name and the VM IP.
  7.The IP address will be used to determine the remaining variables (e.g., paths, folders, etc.) based on the type of VM (e.g., devDB).
*/

  
    $LOCAL_PATH="/home/it490/git/IT490-Spring2023/deployment/package_repo";
    $zipName=$clusterName."_".$listnerName.".zip";
    //Get new package name with the latest version number concactenated
    $newZipName = renameFile($zipName);

    $receiverUser="jonathan";
    $senderUser="jonathan";
    $DeploymentDetails=getDeploymentInfo($clusterName,$listnerName);
    $senderHost=$DeploymentDetails['senderHost'];
    $sourceDir=$DeploymentDetails['sourceDir'];
    $receiverHost=$DeploymentDetails['receiverHost'];
    $receiverFolder=$DeploymentDetails['receiverFolder'];
    $receiverDir = $DeploymentDetails['receiverDir'];
  
    // Call the sendToControlVM function to create a zip and send it to the control VM
    if(sendToControlVM($senderUser, $senderHost, $sourceDir, $zipName, $LOCAL_PATH)){

      //Insert the package name and version into the deploymentdb
      //$newVersion = getLastVersion($zipName) + 0.01;
     // insertPackageDB($zipName, $newVersion);

      // Call the sendToReceiverVM function to pull the zip from the control VM folder and send it to the receiving VM, then unpack it
      sendToReceiverVM($LOCAL_PATH,$zipName ,$receiverUser,$receiverHost, $receiverFolder, $receiverDir );
  
  
  }

    return true;
}
function getDeploymentInfo($clusterName, $listnerName){


if($clusterName="dev" & $listnerName="DB"){

  return array("senderHost" => '192.168.191.15', 'receiverHost'=>"192.168.191.172", 'sourceDir'=>"/home/jonathan/git/IT490-Spring2023/authentication",'receiverDir'=>"/home/jonathan/git/IT490-Spring2023/",'receiverFolder'=>"authentication");
}



}

function getLastVersion($packageName){
  $conn = dbConnection();
  $version = 1.0;
  // lookup package in database

  $sql = "SELECT * FROM deployment.packagelist WHERE name = '$packageName'";
  $result = mysqli_query($conn, $sql);
  $count = mysqli_num_rows($result);

  if ($count != 0) {
    echo 'Package Found' . PHP_EOL;
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

Function insertPackageDB($packageName, $version){
  $conn = dbConnection();

  $sqlInsert = "INSERT into deployment.packagelist (name, version)
                        VALUES ('$packageName','$version')";

  if (mysqli_query($conn, $sqlInsert)){
    echo 'Package insert in db';
    echo $sqlInsert;
    $resp = ['login_status' => true];
    return $resp;
  } else {
    echo "Package insert failed";
  }
}

function renameFile($packageName){
  $latestVersion = getLastVersion($packageName) + 0.01;
  $newName = $packageName."_".$latestVersion; 
  return $newName;
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
    case"deploy":
      return deployment($request['clusterName']   ,$request['listnerName']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

