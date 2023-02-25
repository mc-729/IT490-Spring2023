<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require('safer_echo.php');
require('nav.php');




$type = $_POST['ans'];  
$searchByName= $_POST['searchValue'];

if(isset($type) && isset($searchByName)){


    $client = new rabbitMQClient("RabbitMQConfig.ini", "APIServer");
    $request = '{"type":"'.$type.'","operation": "s","searchTerm": "'.$searchByName.'" }';
    $response = $client->send_request($request);
    print_r($response);

}


//if(!empty('itenName')){}
?>

</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

<h1>Search Type</h1>
<form action="apt_search.php" class="form-inline" method="post">
Search By Name <input type="radio" name="ans" value="SearchByName" /><br />
Search By Ingredient <input type="radio" name="ans" value="SearchbyIngredient"  /><br />
Search by ID <input type="radio" name="ans" value="GetCocktailDetailsByID"  /><br />
Random 10 Cocktails <input type="radio" name="ans" value="Random10Cocktails"  /><br />
Filter by Category <input type="radio" name="ans" value="FilterByCategory"  /><br />
List Ingredients <input type="radio" name="ans" value="ListIngredients"  /><br />
Search Ingredients Info<input type="radio" name="ans" value="SearchIngredientInfo"  /><br />
  <input type="submit" value="submit" />
  <div class="mb-3">
            <label class="form-label" for="searchValue">search here</label>
            <input class="form-control" type="text" id="searchValue" name="searchValue" />
        </div>
</form>





