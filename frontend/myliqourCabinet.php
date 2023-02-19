<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('nav.php');
require_once('safer_echo.php');
$image = "https://www.thecocktaildb.com/images/media/drink/9179i01503565212.jpg";
$test = array("Drink name" => 'long vodka1', 'category' => "Ordinary Drink", 'glass' => "High Ball Glass", 'avg_rating' => "4", 'thumbnail' => $image);
$test1 = array("Drink name" => 'long vodka2', 'category' => "Ordinary Drink", 'glass' => "High Ball Glass", 'avg_rating' => "2", 'thumbnail' => $image);
$test2 = array("Drink name" => 'long vodka3', 'category' => "Ordinary Drink", 'glass' => "High Ball Glass", 'avg_rating' => "1", 'thumbnail' => $image);
$test3 = array("Drink name" => 'long vodka4', 'category' => "Ordinary Drink", 'glass' => "High Ball Glass", 'avg_rating' => "5", 'thumbnail' => $image);
$test4 = array("Drink name" => 'long vodka5', 'category' => "Ordinary Drink", 'glass' => "High Ball Glass", 'avg_rating' => "3", 'thumbnail' => $image);
$test15 = array("Drink name" => 'long vodka6', 'category' => "Ordinary Drink", 'avg_rating' => "3", 'glass' => "High Ball Glass", 'thumbnail' => $image);
$results = array($test, $test1, $test2, $test3, $test4, $test15);

?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

<h1>Cocktail Search</h1>
<form method="GET" class="row row-cols-lg-auto g-3 align-items-center ml-5 mr-5 mb-10"style=" max-width: 120rem; " >
    <div class="input-group  mr-2 mb-3">
    <input class="form-control" type="search" name="itemName" placeholder="Item Filter" style=" max-width: 25rem; " />
        <select method="GET" name="myb" class="form-select" aria-label="Default select example" style=" max-width: 25rem; " >
            <option value="0">--Select Category--</option>
            <?php foreach ($category_list as $dropdown) : ?>

                <option value="<?php se($dropdown, "category");
                                error_log(var_export($dropdown, true)); ?>" name="category">
                    <?php se($dropdown, "category");    ?>
                </option>
            <?php endforeach;  ?>
        </select>

        <select class="form-select" style=" max-width: 15rem; "  name="col" value="<?php se($col); ?>" aria-label="Default select example">
            <option value="0">--Order By--</option>
            <option value="item_price">Cost</option>
            <option value="stock">Stock</option>
            <option value="name">Name</option>
            <option value="avg_rating">Average Rating</option>
            <option value="created">Created</option>
        </select>
        <script>
            //quick fix to ensure proper value is selected since
            //value setting only works after the options are defined and php has the value set prior
            document.forms[0].col.value = "<?php se($col); ?>";
        </script>

        <script>
            //quick fix to ensure proper value is selected since
            //value setting only works after the options are defined and php has the value set prior
            document.forms[0].order.value = "<?php se($order); ?>";
        </script>
        <input class="btn btn-primary" type="submit" value="Search" />


</form>

<div class="row row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-3 g-4 mt-5">
    <?php foreach ($results as $item) : ?>
        <div class="card  mx-auto " style="width: 18rem;">
            <img class="card-img-top" src="<?php se($item, "thumbnail"); ?>" alt="Card image cap">
            <div class="card-body">
                <h5 class="card-title">Name: <?php se($item, "Drink name"); ?></h5>
                <p class="card-text">Average User Rating: ☆ <?php se($item, "avg_rating", "NA"); ?> /5 </p>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Category: <?php se($item, "category"); ?></li>
                <li class="list-group-item">Glass type: <?php se($item, "glass"); ?></li>
            </ul>
            <div class="card-body">
                <a href="#" class="card-link">Cocktail Details</a>

            </div>
            <div class="card-body">
                <a href="#" class="card-link">Add to liqour cabinet</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>