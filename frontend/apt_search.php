<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require('safer_echo.php');
require('nav.php');




$type = $_POST['ans'];
$searchByName = $_POST['searchValue'];

if (isset($type) && isset($searchByName)) {


    $client = new rabbitMQClient("RabbitMQConfig.ini", "APIServer");

    $request = '{"type":"' . $type . '","operation": "s","searchTerm": "' . $searchByName . '" }';

    $response = $client->send_request($request);

    $obj = json_decode($response, true);
}

$count = 0
//change radio button names/values to test new api functionality 



?>

</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

<h1>Search Type</h1>
<form action="apt_search.php" class="form-inline" method="post">
    Search By Name <input type="radio" name="ans" value="SearchByName" /><br />
    Search By Ingredient <input type="radio" name="ans" value="SearchbyIngredient" /><br />
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
        <?php if ($type == "SearchByName" && isset($searchByName)) : ?>

            <?php foreach ($obj['drinks'] as $num) : ?>
                <?php se($count) ?>
                <div class="card" style="width: 80rem;">
                    <img src=" <?php se($obj['drinks'][$count]['strDrinkThumb']) ?>" style=" max-width:20%; max-height:118px;width:auto;height:100%;" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title"> <?php
                                                se($obj['drinks'][$count]['strDrink']); ?></h5>
                        <p class="card-text"> Instructions: <?php se($obj['drinks'][$count]['strInstructions']); ?></p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php for ($x = 0; $x <= 15; $x++) : ?>
                            <?php $strIngredient = "strIngredient" . $x;
                            if (isset($obj['drinks'][$count][$strIngredient])) : ?>
                                <li class="list-group-item">Ingredient <?php se($x) ?>: <?php se($obj['drinks'][$count][$strIngredient]) ?></li>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <?php for ($y = 0; $y <= 15; $y++) : ?>
                            <?php $strmeasure = "strMeasure" . $y; ?>

                            <?php
                            if (!empty($obj['drinks'][$count][$strmeasure])) : ?>
                                <li class="list-group-item">Measurement <?php se($y) ?>: <?php se($obj['drinks'][$count][$strmeasure]) ?></li>
                            <?php endif; ?>
                        <?php 
                        endfor; ?>
                    </ul>
                    <div class="card-body">
                        <a href="#" class="card-link">Card link</a>
                        <a href="#" class="card-link">Another link</a>
                    </div>
                </div>
            <?php $count++; endforeach; ?>
        <?php endif; ?>
    </ul>
</div>