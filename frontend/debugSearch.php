<?php

require_once 'path.inc';
require_once 'get_host_info.inc';
require_once 'rabbitMQLib.inc';
require 'safer_echo.php';
require 'nav.php';

$type = $_POST['ans'];
$searchByName = $_POST['searchValue'];

if (isset($type) && isset($searchByName)) {
    $client = new rabbitMQClient('RabbitMQConfig.ini', 'testServer');
    $request = [];
    $request['type'] = 'API_CALL';
    $request['key'] =
        '{"type":"' .
        $type .
        '","operation": "s","searchTerm": "' .
        $searchByName .
        '" }';

    $response = $client->send_request($request);

    $obj = json_decode($response, true);
}

$count = 0;

//change radio button names/values to test new api functionality
?>

</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

<h1>Search Type</h1>
<form action="debugSearch.php" class="form-inline" method="post">
    Search By Name <input type="radio" name="ans" value="SearchByName" /><br />
    Search By Ingredient <input type="radio" name="ans" value="SearchBySingleIngredient" /><br />
    Search by ID <input type="radio" name="ans" value="GetCocktailDetailsByID" /><br />
    Random 10 Cocktails <input type="radio" name="ans" value="Random10Cocktails" /><br />
    Filter by Category <input type="radio" name="ans" value="FilterByCategory" /><br />
    List Ingredients <input type="radio" name="ans" value="ListIngredients" /><br />
    Search Ingredients Info<input type="radio" name="ans" value="SearchIngredientInfo" /><br />
    <input type="submit" value="submit" />
    <div class="mb-3">
        <label class="form-label" for="searchValue">search here</label>
        <input class="form-control" type="text" id="searchValue" name="searchValue" />
    </div>

</form>
<div class="container list-group infinite-scroll" id="basic" style="max-height: 1000px; overflow-y: scroll;">

    <ul class="container list-group infinite-scroll" id="basic-example" style="max-height: 400px; overflow-y: scroll;">
        <?php if (isset($type) && isset($searchByName)): ?>
            <?php foreach ($obj['drinks'] as $num): ?>
                <?php foreach ($obj['drinks'][$count++] as $key => $value): ?>
                    <li class="list-group-item">The key is <?php se(
                        $key
                    ); ?> and the value is <?php se($value); ?> </li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>