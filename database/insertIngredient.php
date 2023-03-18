#!/usr/bin/php
<?php
$servername = "localhost";
$username = "testuser";
$password = "12345";
$dbname = "IT490";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else 
{
	echo "Successfully Connected!".PHP_EOL;
}

//sql to create sessions table

$sql = "INSERT INTO IT490.ingredients(name) 
VALUES
  ('Light rum'),
  ('Applejack'),
  ('Gin'),
  ('Dark rum'),
  ('Sweet Vermouth'),
  ('Strawberry schnapps'),
  ('Scotch'),
  ('Apricot brandy'),
  ('Triple sec'),
  ('Southern Comfort'),
  ('Orange bitters'),
  ('Brandy'),
  ('Lemon vodka'),
  ('Blended whiskey'),
  ('Dry Vermouth'),
  ('Amaretto'),
  ('Tea'),
  ('Champagne'),
  ('Coffee liqueur'),
  ('Bourbon'),
  ('Tequila'),
  ('Vodka'),
  ('Anejo rum'),
  ('Bitters'),
  ('Sugar'),
  ('Kahlua'),
  ('demerara Sugar'),
  ('Dubonnet Rouge'),
  ('Watermelon'),
  ('Lime juice'),
  ('Irish whiskey'),
  ('Apple brandy'),
  ('Carbonated water'),
  ('Cherry brandy'),
  ('Creme de Cacao'),
  ('Grenadine'),
  ('Port'),
  ('Coffee brandy'),
  ('Red wine'),
  ('Rum'),
  ('Grapefruit juice'),
  ('Ricard'),
  ('Sherry'),
  ('Cognac'),
  ('Sloe gin'),
  ('Apple juice'),
  ('Pineapple juice'),
  ('Lemon juice'),
  ('Sugar syrup'),
  ('Milk'),
  ('Strawberries'),
  ('Chocolate syrup'),
  ('Yoghurt'),
  ('Mango'),
  ('Ginger'),
  ('Lime'),
  ('Cantaloupe'),
  ('Berries'),
  ('Grapes'),
  ('Kiwi'),
  ('Tomato juice'),
  ('Cocoa powder'),
  ('Chocolate'),
  ('Heavy cream'),
  ('Galliano'),
  ('Peach Vodka')";

if ($conn->query($sql) === TRUE) {
	echo "Table sessions created successfully".PHP_EOL;
} else {
	echo "Error creating table sessions: " .$conn->error;
}

$conn->close();
?>