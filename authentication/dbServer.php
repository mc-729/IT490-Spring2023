#!/usr/bin/php
<?php
require_once 'path.inc';
require_once 'get_host_info.inc';
require_once 'rabbitMQLib.inc';
//require_once '../Logging/send_log.inc';
//require_once __DIR__ . '/../vendor/autoload.php';


function loginAuth($username, $password)
{
    $conn = dbConnection();

    // lookup username in database

    $sql = "SELECT * FROM IT490.Users WHERE Email = '$username'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $count = mysqli_num_rows($result);

    if ($count != 0) {
        echo 'User Found' . PHP_EOL;

        // Verify password
        $sql2 = "SELECT Password FROM IT490.Users WHERE Email = '$username'";
        $result2 = mysqli_query($conn, $sql2);
        $row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
        $hashedpass = $row2['Password'];

        echo $hashedpass . PHP_EOL;

        if (password_verify($password, $hashedpass)) {
            echo 'Login Successful' . PHP_EOL;
            $resp = array(
                'login_status' => true,
                'session_id' => SessionGen($row['User_ID']),
                'user_id' => $row['User_ID'],
                'first_name' => $row['F_Name'],
                'last_name' => $row['L_Name'],
                'username' => $row['Username'],
                'email' => $row['Email'],
                'city' => $row['City'],
                'state' => $row['State']
            );
            return $resp;
        } else {
            echo 'Login Failed' . PHP_EOL;
            $resp = array(
                'login_status' => false,
                'session_id' => null,
                'user_id' => null,
                'first_name' => null,
                'last_name' => null,
                'username' => null,
                'email' => null,
                'city' => null,
                'state' => null
            );
            return $resp;
        }
    } else {
        echo 'Login Failed' . PHP_EOL;
        $resp = array(
            'login_status' => false,
            'session_id' => null,
            'user_id' => null,
            'first_name' => null,
            'last_name' => null,
            'username' => null,
            'email' => null,
            'city' => null,
            'state' => null
        );
        return $resp;
    }
} //End loginAuth

function dbConnection()
{
    $servername = '192.168.191.69';
    $uname = 'testuser';
    $pw = '12345';
    $dbname = 'IT490';
    
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
function registrationInsert($username, $password, $email, $firstName, $lastName, $city, $state)
{
    $conn = dbConnection();

    $sqlRegi = "SELECT * FROM IT490.Users WHERE Email = '$email'";
    $resultRegi = mysqli_query($conn, $sqlRegi);
    $rowRegi = mysqli_fetch_array($resultRegi, MYSQLI_ASSOC);
    $countRegi = mysqli_num_rows($resultRegi);
    $hashPassword = password_hash($password, PASSWORD_DEFAULT);

    if ($countRegi == 1) {
        // ==1 means found an already existing Username/Email in IT490.Users
        echo 'Username/Email already exists, please use a different one.' .
            PHP_EOL;
        $resp = ['login_status' => false];
        return $resp;
    }
    //If Username/Email is not found in database/doesn't exist, do this
    else {
        $sqlInsert = "INSERT into IT490.Users (Username,F_Name, L_Name, City, State, Email, Password)
                        VALUES ('$username','$firstName','$lastName','$city', '$state', '$email','$hashPassword')";

        if (mysqli_query($conn, $sqlInsert)) {
            echo 'New user registered, welcome. ';
            echo $sqlInsert;
            $resp = ['login_status' => true];
            return $resp;
        } else {
            /* $msg = 'Error with query';
            $request = [];
            $request['type'] = 'error';
            $request['service'] = 'database';
            $request['message'] = $msg;
            sendLog($request); */
            echo "we failed to insert bbby";
        }
    }
} // End registrationInsert

function SessionGen($user_ID)
{
    $conn = dbConnection();

    $check = "SELECT * from IT490.sessions where UID = $user_ID";
    $query = mysqli_query($conn, $check);
    $count = mysqli_num_rows($query);

    $sessionID = rand(1000, 99999999);
    $query2 = "INSERT into IT490.sessions(UID,SessionID)VALUES('$user_ID','$sessionID')";
    $result = mysqli_query($conn, $query2);
    return $sessionID;
} // End SessionGen

function doValidate($sessionid)
{
    $count = 0;
    if (!is_null($sessionid)) {
        $conn = dbConnection();
        $sql = "SELECT * FROM IT490.sessions WHERE SessionID = '$sessionid'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $count = mysqli_num_rows($result);
    }
    echo $count;
    if ($count != 0) {
        echo 'Session is valid' . PHP_EOL;

        $resp = ['session_status' => true];
        return $resp;
    } else {
        echo 'Session is not valid' . PHP_EOL;
        $resp = ['session_status' => false];
        return $resp;
    }
} // End doValidate

function logout($sessionid)
{
    $conn = dbConnection();
    $query = "DELETE FROM IT490.sessions WHERE SessionID = '$sessionid'";

    if (mysqli_query($conn, $query)) {
        return true;
    } else {
        return false;
    }
}



function eventInsert($name, $UID, $description, $date, $url, $image)
{
    $conn = dbConnection();
    echo "DB connected";
    $date_str = date_create_from_format('M d', $date);
    $date_str->setDate(date('Y'), $date_str->format('m'), $date_str->format('d'));
    $formatted_date = date_format($date_str, 'Y-m-d');

    $name = mysqli_real_escape_string($conn, $name);
    $description = mysqli_real_escape_string($conn, $description);

    $sqlInsert = "INSERT into IT490.events (UID, name, description, image, link, startdate)
        VALUES ('$UID','$name','$description','$image','$url', '$formatted_date')";

    if (mysqli_query($conn, $sqlInsert)) {
        echo 'Event Saved';
        echo $sqlInsert;

        return true;
    } else {
        $msg = 'Error with query';
        $request = [];
        $request['type'] = 'error';
        $request['service'] = 'database';
        $request['message'] = $msg;
        sendLog($request);
        echo "we failed to insert bbby";
        return false;
    }
} // End eventInsert

function eventDelete($name, $UID)
{
    $conn = dbConnection();
    echo "DB connected";

    $sql = "DELETE FROM IT490.events WHERE name = '$name' AND UID = '$UID'";

    if (mysqli_query($conn, $sql)) {
        echo 'Event Deleted' . PHP_EOL;
        echo $sql;

        return true;
    } else {
        $msg = 'Error with query' . PHP_EOL;
        $request = [];
        $request['type'] = 'error';
        $request['service'] = 'database';
        $request['message'] = $msg;
        sendLog($request);
        echo "we failed to delete bbby" . PHP_EOL;
        return false;
    }
} // End eventDelete


function updateProfile($sessionid, $username, $newpassword, $oldpassword, $email, $firstName, $lastName, $city, $state)
{

    // Connect to the database
    $conn = dbConnection();

    // Build the SQL statement
    $sql = "UPDATE Users SET";

    if (doValidate($sessionid)) {
        $sql2 = "SELECT UID FROM IT490.sessions WHERE sessionID = '$sessionid'";
        $result = mysqli_query($conn, $sql2);
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $userid = $row['UID'];


        if (!empty($newpassword) && !empty($oldpassword)) {
            $sql2 = "SELECT Password FROM IT490.Users WHERE User_ID = '$userid'";
            $result2 = mysqli_query($conn, $sql2);
            $row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
            $hashedpass = $row2['Password'];
            if (password_verify($oldpassword, $hashedpass)) {
                $hashPassword = password_hash($newpassword, PASSWORD_DEFAULT);
                $sql .= " Password='" . mysqli_real_escape_string($conn, $hashPassword) . "',";
            }
        }

        if (!empty($username)) {
            $sql .= " Username='" . mysqli_real_escape_string($conn, $username) . "',";
        }

        if (!empty($email)) {
            $sql .= " Email='" . mysqli_real_escape_string($conn, $email) . "',";
        }
        if (!empty($firstName)) {
            $sql .= " f_name='" . mysqli_real_escape_string($conn, $firstName) . "',";
        }
        if (!empty($lastName)) {
            $sql .= " l_name='" . mysqli_real_escape_string($conn, $lastName) . "',";
        }
        if (!empty($city)) {
            $sql .= " city='" . mysqli_real_escape_string($conn, $city) . "',";
        }
        if (!empty($state)) {
            $sql .= " state='" . mysqli_real_escape_string($conn, $state) . "',";
        }

        // Remove the trailing comma from the SQL statement
        $sql = rtrim($sql, ",");

        // Add the WHERE clause to the SQL statement
        $sql .= " WHERE User_ID=" . $userid;

        // Execute the SQL statement
        $result = mysqli_query($conn, $sql);
        if ($result) {
            return true;
        }
        // Remove the trailing comma from the SQL statement
        $sql = rtrim($sql, ",");

        // Add the WHERE clause to the SQL statement
        $sql .= " WHERE User_ID=" . $userid;

        // Execute the SQL statement
        $result = mysqli_query($conn, $sql);
        if ($result) {
            return true;
        }
    } else {
        logout($sessionid);
    }
}

function storeSearchResultsInCache($query, $searchResults)
{

    $searchResults = json_decode($searchResults, true);
    $count = 0;



    // Convert results to JSON
    $json = json_encode($searchResults);
    $filtered_json = "[" . filter_var($json) . "]";
    $query = implode(',', $query);
    //print_r($json);



    // Insert JSON data into database using prepared statement

    $conn = dbConnection();
    $stmt = $conn->prepare('INSERT INTO IT490.Cache (SearchKey, Results) VALUES (?,?)');
    $stmt->bind_param('ss', $query, $filtered_json);
    $result = $stmt->execute();
    echo $result;
    $stmt->close();
    $conn->close();

    // Check for errors and return result
    if ($result) {
        echo "It has been added to the cache " . PHP_EOL;

        return true;
    } else {
        echo "Something went wrong in the cache" . PHP_EOL;
        return false;
    }
}

function requestEmail($userid)
{
    $conn = dbConnection();
    $query = "SELECT Email FROM IT490.Users WHERE User_Id = '$userid'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $email = $row['Email'];
    echo $email . PHP_EOL;
    mysqli_close($conn);
    return $email;
} // End requestEmail

function requestEvents($timeleft)
{
    $conn = dbConnection();
    $query = "SELECT * FROM IT490.events WHERE timeleft <= '$timeleft'";
    $result = mysqli_query($conn, $query);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    //mysqli_free_result($rows);
    //echo $rows . PHP_EOL;
    mysqli_close($conn);
    return $rows;
} // End requestEvents

function fetchSearchResultsCached($query, $loginStatus,$filterby)


{
    try {
        echo "did we make it here?" . PHP_EOL;

        $strQuery = implode(',', $query);
        $conn = dbConnection();
        $sql = "SELECT * FROM IT490.Cache WHERE SearchKey = '$strQuery'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $count = mysqli_num_rows($result);




        if ($count == 0) {
            echo "it was not in cache" . PHP_EOL;
            $client = new rabbitMQClient('RabbitMQConfig.ini', 'APIServer');

            $searchResults = $client->send_request($query);
            if (isset($searchResults)) {
                storeSearchResultsInCache($query, $searchResults);
                $strQuery = implode(',', $query);
                $conn = dbConnection();
                $sql = "SELECT * FROM IT490.Cache WHERE SearchKey = '$strQuery'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                if ($query['type'] == 'GoogleEventSearch') {
                    return $row['Results'];
                } else {
                    $drinks = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    echo "ADDING LIKES TO RESPONSE ARRAY" . PHP_EOL;
                    $drinksList = getDrinkTotalRating($row['Results'], $loginStatus,$filterby);


                    return $drinksList;
                }
            }
        } else if ($count != 0) {
            echo "it was in cache" . PHP_EOL;

            if ($query['type'] == 'GoogleEventSearch') {
                return $row['Results'];
            } else {
                $drinksList = getDrinkTotalRating($row['Results'], $loginStatus,$filterby);
                return $drinksList;
            }
        }
    } catch (Exception $e) {
        echo 'Caught exception my dude: ',  $e->getMessage(), "\n";
        return  $resp = ['API_REQUEST_STATUS' => false];
    }
}


function getDrinkTotalRating($drinks, $loginStatus,$filterby)

{
    $conn = dbconnection();
    $totalLikes = array();
    $drinks = json_decode($drinks, true);
    $drinks = $drinks[0];
    if (is_string($drinks)) {
        $drinks = json_decode($drinks, true);
    }
    $drinks = $drinks['drinks'];

    $length = count($drinks);

    for ($i = 0; $i < $length; $i++) {
        $drinkName = $drinks[$i]["strDrink"];
        $drinkName = mysqli_real_escape_string($conn, $drinkName);
        $sql = "SELECT DrinkName FROM IT490.UserCocktails WHERE DrinkName = '$drinkName'";
        $result = $conn->query($sql);
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $num_likes = count($rows);
        $drinks[$i]['likes'] = $num_likes;
    }
    if (isset($loginStatus)) {
        $sql = "SELECT UID FROM IT490.sessions WHERE sessionID = '$loginStatus'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $userid = $row['UID'];
        for ($i = 0; $i < $length; $i++) {
            $drinkName = $drinks[$i]["strDrink"];
            $drinkName = mysqli_real_escape_string($conn, $drinkName);
            $sql = "SELECT DrinkName FROM IT490.UserCocktails WHERE DrinkName = '$drinkName' and User_ID='$userid'";
            $result = $conn->query($sql);
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $num_likes = count($rows);
            if ($num_likes > 0) {
                $drinks[$i]['userLikes'] = true;
            } else $drinks[$i]['userLikes'] = false;
           
        }
        if($filterby){
        $test = ReccommendMe($userid, $drinks, $length);
        return $test;
    }}

    print_r($totalLikes) . PHP_EOL;
    return $drinks;
}
function ReccommendMe($userID, $DrinkList, $length)
{
    $conn = dbConnection();
    $sql = "SELECT ING_Name from UserMLC where User_ID ='$userID'";
    $result = $conn->query($sql);
    $response = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $ingredientList=array();
    foreach ($response as $ingredient)
    {
      
        array_push($ingredientList, $ingredient["ING_Name"]);
    }
    $ReturnDrinkList=array();


    for ($i = 0; $i < $length; $i++) {
        $DrinkList[$i]['show'] = false;

        for ($x = 0; $x <= 15; $x++) {
            $strIngredient = "strIngredient" . $x;
            if (isset($DrinkList[$i][$strIngredient]) && in_array($DrinkList[$i][$strIngredient],   $ingredientList)) {
           
               
                    echo  "we made itpast bool". $DrinkList[$i][$strIngredient] . PHP_EOL;
                    $DrinkList[$i]['show'] = true;
                    array_push($ReturnDrinkList,$DrinkList[$i]);
                    break;
              
            }


        }
      
      
    }
    return $ReturnDrinkList;
}
function retrieveRecipes($sessionid)

{
    $conn = dbconnection();
    if (doValidate($sessionid)) {

        $sql = "SELECT UID FROM IT490.sessions WHERE sessionID = '$sessionid'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $userid = $row['UID'];
        $sql = "SELECT name FROM IT490.ingredients";
        $sql2 = "SELECT Recipe FROM IT490.UserCocktails WHERE User_ID = $userid";
        $result2 = $conn->query($sql2);

        $result3 = $conn->query($sql);
        $ingredients = mysqli_fetch_all($result3, MYSQLI_ASSOC);
        $drinkList = mysqli_fetch_all($result2, MYSQLI_ASSOC);
        $userIngredients = GetUsieringredients($userid);
        print_r($ingredients);
        $resp = array(
            'ingredients' => $ingredients,
            'drinkList' => $drinkList,
            'userIngredients' => $userIngredients

        );
        return $resp;
    }
}

function GetUsieringredients($userID)
{

    $conn = dbConnection();
    $sql = "SELECT ING_Name, Amount,Measurement_Type from UserMLC where User_ID ='$userID'";
    $result = $conn->query($sql);
    $response = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $response;
}

function DeleteRecipe($sessionID, $drinkName)
{
    $conn = dbconnection();
    if (doValidate($sessionID)) {

        $sql = "SELECT UID FROM IT490.sessions WHERE sessionID = '$sessionID'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $userid = $row['UID'];
        $sql = "DELETE FROM IT490.UserCocktails where User_ID=$userid and DrinkName='$drinkName'";


        if ($conn->query($sql))  return  ['Status' => true];
        else  return  ['Status' => false];
    }
}

function updateRecipeList($sessionid, $recipedata, $drinkname)
{
    $conn = dbConnection();
    if (doValidate($sessionid)) {
        $sql = "SELECT UID FROM IT490.sessions WHERE sessionID = '$sessionid'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $userid = $row['UID'];
        //$recipedata = str_replace("'", '"', $recipedata);
        // $recipedata = str_replace("None", "null", $recipedata);
        // $recipelist =json_encode($recipelist,  JSON_UNESCAPED_UNICODE|JSON_FORCE_OBJECT). "\n";
        echo $recipedata . PHP_EOL;
        $recipedata = json_encode($recipedata, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $conn = dbConnection();
        $stmt = $conn->prepare('INSERT INTO IT490.UserCocktails (User_ID,Recipe,DrinkName) VALUES (?,?,?)');
        $stmt->bind_param('sss', $userid, $recipedata, $drinkname);
        $result = $stmt->execute();
        echo $result;
        $stmt->close();
        $conn->close();

        if ($result) {
            echo "Recipe updated successfully" . PHP_EOL;
        } else {
            echo "An error occurred while updating the recipe" . PHP_EOL;
        }
    }
}






function updateDates()
{
    $conn = dbConnection();
    $query = "SELECT * FROM IT490.events";
    $result = mysqli_query($conn, $query);
    $today = date("Y-m-d");
    while ($rows = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $eventId = $rows['id'];
        $eventDate = $rows['startdate'];
        $timeleft = (strtotime($eventDate) - strtotime($today));
        $days = floor($timeleft / (60 * 60 * 24));
        $query = "UPDATE IT490.events SET timeleft = $days WHERE id = $eventId";

        if (mysqli_query($conn, $query)) {
            echo 'Timeleft updated' . PHP_EOL;
        } else {
            echo "we failed to update bbby" . PHP_EOL;
            return false;
        }
    }
    mysqli_close($conn);
    return true;
} // End requestEvents





function updateUserMLC($sessionid, $ingName, $amount, $measurementType)
{
    // Connect to the database
    $conn = dbConnection();

    if (doValidate($sessionid)) {
        $sql = "SELECT UID FROM IT490.sessions WHERE sessionID = '$sessionid'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $userid = $row['UID'];

        //Check
        $sqlCheck = "SELECT * FROM IT490.UserMLC WHERE User_ID = $userid and Ing_Name = '$ingName'";
        $result = mysqli_query($conn, $sqlCheck);
        $count = mysqli_num_rows($result);
        if ($count == 0) {

            $sql = "INSERT into IT490.UserMLC (User_ID, Ing_Name, Amount, Measurement_Type) 
        VALUES ('$userid', '$ingName', '$amount', '$measurementType')";
        } else {
            $sql = "UPDATE UserMLC SET Amount = '$amount', Measurement_Type = '$measurementType'
    WHERE User_ID = '$userid' AND Ing_Name = '$ingName'";
        }
        $result = mysqli_query($conn, $sql);

        if ($result) {
            echo "UserMLC table updated successfully" . PHP_EOL;

            $sql = "SELECT * FROM UserMLC WHERE User_ID = '$userid' AND Ing_Name = '$ingName'";
            $result = mysqli_query($conn, $sql);
            $return = mysqli_fetch_array($result);
            return $return;
        } else {
            echo "An error occurred while updating the table" . PHP_EOL;
        }
    }
}






function requestProcessor($request)
{
    echo 'received request' . PHP_EOL;
    var_dump($request);
    if (!isset($request['type'])) {
        return 'ERROR: unsupported message type';
    }
    switch ($request['type']) {
        case 'Login':
            return loginAuth($request['username'], $request['password']);
        case 'Register':
            return registrationInsert(
                $request['username'],
                $request['password'],
                $request['email'],
                $request['firstName'],
                $request['lastName'],
                $request['city'],
                $request['state']
            );
        case 'validate_session':
            return doValidate($request['sessionID']);
        case 'Logout':
            return logout($request['sessionID']);
        case 'API_CALL':

            return fetchSearchResultsCached($request['key'], $request['loginStatus'],$request['filterby']);
        case "Update":

            return updateProfile(
                $request['sessionID'],
                $request['username'],
                $request['newPW'],
                $request['oldPW'],
                $request['email'],
                $request['firstName'],
                $request['lastName'],
                $request['city'],
                $request['state']
            );


        case 'SaveEvent':
            return eventInsert($request['name'], $request['UID'], $request['description'], $request['date'], $request['URL'], $request['image']);
        case "Email":

            return requestEmail($request['userid']);
        case "Events":
            return requestEvents($request['timeleft']);

        case "totallikes":
            return getDrinkTotalRating($request['drinks'], $request['sessionID']);

        case "like":
            return updateRecipeList($request['sessionID'], $request['drink'], $request['drinkName']);
        case "retrieveRecipe":
            return retrieveRecipes($request['sessionID']);

        case "updateMLC":
            return updateUserMLC($request['sessionID'], $request['ingName'], $request['amount'], $request['measurementType']);
        case "UpdateStartDates":
            return updateDates();
        case "deleteRecipe":
            return DeleteRecipe($request['sessionID'], $request['drinkName']);

        case "DeleteEvent":
            return eventDelete($request['name'], $request['UID']);
    }

    return [
        'returnCode' => '0',
        'message' => 'Server received the request and processed it.',
    ];
} // End requestProcessor

$server = new rabbitMQServer('RabbitMQConfig.ini', 'testServer');

echo 'Authentication Server BEGIN TRY' . PHP_EOL;
$server->process_requests('requestProcessor');

echo 'Authentication Server try END' . PHP_EOL;
exit();


?>