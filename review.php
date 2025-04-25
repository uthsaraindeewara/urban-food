<?php
require 'connection1.php';

// Now choose the database and collection as needed here
$db = $client->selectDatabase('productdb');
$reviewsCollection = $db->selectCollection('reviews');

// Fetch and display data
$reviews = $reviewsCollection->find();

foreach ($reviews as $review) {
    echo "Product: " . $review['product_id'] . "<br>";
    echo "User: " . $review['user_id'] . "<br>";
    echo "Value: " . $review['value'] . "<br>";
    echo "<hr>";
}
?>