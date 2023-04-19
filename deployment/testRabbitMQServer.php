#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
global $LOCAL_PATH;
$LOCAL_PATH="/home/it490/git/IT490-Spring2023/deployment/package_repo";
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

function sendToControlVM($senderUser, $senderHost, $sourceDir, $zipName, $localPath, $excludePath = '')
{

  $excludeOption = '';
  if (!empty($excludePath)) {
      $excludeOption = " -x '$excludePath/*'";
  }

    $bashScript = <<<BASH
#!/bin/bash

# Configuration
REMOTE_USER="$senderUser"
REMOTE_PATH="$sourceDir"
LOCAL_PATH="$localPath"
ZIP_NAME="$zipName"
REMOTE_HOST="$senderHost"
EXCLUDE_OPTION="$excludeOption"
# Zip files on the remote server
ssh "\${REMOTE_USER}@\${REMOTE_HOST}" "cd \${REMOTE_PATH} && zip -r \${ZIP_NAME} . --exclude *.ini\${EXCLUDE_OPTION}"



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
  // echo $output;
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
  5.Extracts the zip archive to the specified directory on the rec#!/bin/bash

# Configuration
REMOTE_USER="jonathan"
REMOTE_PATH="/home/jonathan/git/IT490-Spring2023/authentication"
LOCAL_PATH="/home/it490/git/IT490-Spring2023/deployment/package_repo"
ZIP_NAME="dev_frontend_1.02.zip"
REMOTE_HOST="192.168.191.15"
# Zip files on the remote server
ssh "${REMOTE_USER}@${REMOTE_HOST}" "cd ${REMOTE_PATH} && zip -r ${ZIP_NAME} . --exclude *.ini"
"

# Rsync the zipped file to the local machine
rsync -avzP --remove-source-files "${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}/${ZIP_NAME}" "${LOCAL_PATH}/${ZIP_NAME}"

iving VM
  6.In the final version of this function, the user will only need to provide the cluster name and the VM IP.
  7.The IP address will be used to determine the remaining variables (e.g., paths, folders, etc.) based on the type of VM (e.g., devDB).
*/

  
    global $LOCAL_PATH;
    $packageName=$clusterName."_".$listnerName;
    //Get new package name with the latest version number concactenated
    $versionDetails= renameFile($packageName);
    $zipName=$versionDetails["name"];
    $versionNum=$versionDetails["version"];

    $receiverUser="jonathan";
    $senderUser="jonathan";
    $DeploymentDetails=getDeploymentInfo($clusterName,$listnerName);
    $senderHost=$DeploymentDetails['senderHost'];
    $sourceDir=$DeploymentDetails['sourceDir'];
    echo $sourceDir. PHP_EOL;;
    $receiverHost=$DeploymentDetails['receiverHost'];
    $receiverFolder=$DeploymentDetails['receiverFolder'];
    $receiverDir = $DeploymentDetails['receiverDir'];
    $exclude= isset($DeploymentDetails['exclude'])  ?  $DeploymentDetails['exclude']  : null;
    // Call the sendToControlVM function to create a zip and send it to the control VM
    $var=sendToControlVM($senderUser, $senderHost, $sourceDir, $zipName, $LOCAL_PATH,$exclude);
if($var){
      //Insert the package name and version into the deploymentdb
     // $newVersion = getLastVersion($zipName) + 0.01;
      //echo 'new version '.$newVersion . PHP_EOL;
   
       //insertPackageDB($packageName, $versionNum);

      // Call the sendToReceiverVM function to pull the zip from the control VM folder and send it to the receiving VM, then unpack it
     sendToReceiverVM($LOCAL_PATH,$zipName ,$receiverUser,$receiverHost, $receiverFolder, $receiverDir );
     
  
  

    return true;}

}
function getDeploymentInfo($clusterName, $listnerName){



  if($clusterName=="dev" and $listnerName=="DB"){
    echo "hello frontend dev DB";
  
    return array("senderHost" => '192.168.191.15', 'receiverHost'=>"192.168.191.172", 'sourceDir'=>"/home/jonathan/git/IT490-Spring2023/authentication",'receiverDir'=>"/home/jonathan/git/IT490-Spring2023/",'receiverFolder'=>"authentication");
  }
  if($clusterName=="dev" and $listnerName=="API"){
  
    return array("senderHost" => '192.168.191.15', 'receiverHost'=>"192.168.191.172", 'sourceDir'=>"/home/jonathan/git/IT490-Spring2023/API",'receiverDir'=>"/home/jonathan/git/IT490-Spring2023/",'receiverFolder'=>"API");
  }
  if($clusterName=="dev" and $listnerName=="frontend"){
    echo "hello frontend dev";
  
    return array("senderHost" => '192.168.191.15', 'receiverHost'=>"192.168.191.172", 'sourceDir'=>"/var/www/MyLiqourCabinet/application",'receiverDir'=>"/var/www/MyLiqourCabinet",'receiverFolder'=>"application","exclude"=>"rabbitMQ");
  }
  if($clusterName=="qa"and $listnerName=="DB"){
 
    return array("senderHost" => '192.168.191.15', 'receiverHost'=>"192.168.191.172", 'sourceDir'=>"/home/jonathan/git/IT490-Spring2023/authentication",'receiverDir'=>"/home/jonathan/git/IT490-Spring2023/",'receiverFolder'=>"authentication");
  }
  if($clusterName=="qa" and $listnerName=="API"){
  
    return array("senderHost" => '192.168.191.15', 'receiverHost'=>"192.168.191.172", 'sourceDir'=>"/home/jonathan/git/IT490-Spring2023/API",'receiverDir'=>"/home/jonathan/git/IT490-Spring2023/",'receiverFolder'=>"API");
  }
  if($clusterName=="qa" and $listnerName=="frontend"){
  
    return array("senderHost" => '192.168.191.15', 'receiverHost'=>"192.168.191.172", 'sourceDir'=>"/var/www/MyLiqourCabinet/application",'receiverDir'=>"/var/www/MyLiqourCabinet",'receiverFolder'=>"application","exclude"=>"rabbitMQ");
  }
  if($clusterName=="prod" and $listnerName=="DB"){
  
    return array("senderHost" => '192.168.191.15', 'receiverHost'=>"192.168.191.172", 'sourceDir'=>"/home/jonathan/git/IT490-Spring2023/authentication",'receiverDir'=>"/home/jonathan/git/IT490-Spring2023/",'receiverFolder'=>"authentication");
  }
  if($clusterName=="prod" and $listnerName=="API"){
  
    return array("senderHost" => '192.168.191.15', 'receiverHost'=>"192.168.191.172", 'sourceDir'=>"/home/jonathan/git/IT490-Spring2023/API",'receiverDir'=>"/home/jonathan/git/IT490-Spring2023/",'receiverFolder'=>"API");
  }
  if($clusterName=="prod" and $listnerName=="frontend"){
  
    return array("senderHost" => '192.168.191.15', 'receiverHost'=>"192.168.191.172", 'sourceDir'=>"/var/www/MyLiqourCabinet/application",'receiverDir'=>"/var/www/MyLiqourCabinet",'receiverFolder'=>"application","exclude"=>"rabbitMQ");
  }
  
  


}

function getLastVersion($packageName){
  $conn = dbConnection();

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
    return 1.0;
  } 
}

Function insertPackageDB($packageName, $version){
  $conn = dbConnection();
 
  $sqlInsert = "INSERT into deployment.packagelist (name, version)
                        VALUES ('$packageName','$version')";

  if (mysqli_query($conn, $sqlInsert)){
    echo 'Package insert in db hello';
    echo $sqlInsert;
    $resp = ['login_status' => true];
    return $resp;
  } else {
    echo "Package insert failed";
  }
}

function renameFile($packageName, ){
  $latestVersion = getLastVersion($packageName) + 0.01;
  echo $latestVersion." latest version ". PHP_EOL;
  $newName = $packageName."_".$latestVersion.".zip"; 
  echo $newName." new package name". PHP_EOL;
  insertPackageDB($packageName, $latestVersion);
  return array("name"=>$newName,"version"=>$latestVersion);
}

function getStableVersion($clusterName, $listnerName){
  $deploymentDetails = getDeploymentInfo($clusterName,$listnerName);
  $packageName=$clusterName."_".$listnerName;

  $conn = dbConnection();
  
 // lookup package in database




 

   
   $sql2 = "SELECT * FROM deployment.packagelist WHERE name = '$packageName' AND status = 1 ORDER BY version DESC LIMIT 1";
   echo "sql query".$sql2.PHP_EOL;
   $result2 = mysqli_query($conn, $sql2);
   $row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
   print_r($row2);
   $name = $row2['name'];

   $version = $row2['version'];

   $packageDetails = array('name'=> $name, 'latestStableVersion'=> $version);
   echo "package details in getStable ".$packageDetails.PHP_EOL;
   return $packageDetails;
  
}
function addStatus($clusterName, $listnerName, $status){
  // 0 for fail and 1 for success
  $conn = dbConnection();
  $packageName=$clusterName."_".$listnerName; 
  $version=getLastVersion($packageName);
  $sql = "UPDATE deployment.packagelist SET status = '$status' WHERE name = '$packageName' and version='$version'";

  if (mysqli_query($conn, $sql)){
      echo 'Package status insert in db'. PHP_EOL;
      echo $sql;
      return true;
    } else {
      echo "Package status insert failed". PHP_EOL;
      return false;
    }
  
}
function rollBack($clusterName, $listnerName){
  echo "Checking latest stable packages". PHP_EOL;
  global $LOCAL_PATH;
  $latestStable=getStableVersion($clusterName, $listnerName);
  print_r($latestStable);
  $zipName = $latestStable['name']."_".$latestStable['latestStableVersion'].".zip";
  echo "zip name ".$zipName.PHP_EOL;
  $DeploymentDetails = getDeploymentInfo($clusterName, $listnerName);
  $receiverUser='jonathan';
  $receiverHost=$DeploymentDetails['receiverHost'];
  $receiverFolder=$DeploymentDetails['receiverFolder'];
  $receiverDir = $DeploymentDetails['receiverDir'];
  sendToReceiverVM($LOCAL_PATH, $zipName, $receiverUser, $receiverHost, $receiverFolder, $receiverDir);
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
    case"addStatus":
        return addStatus($request['clusterName']   ,$request['listnerName'], $request['status']);
    case"rollback":
      return rollBack($request['clusterName'], $request['listnerName']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

