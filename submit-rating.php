<?php
require 'connection1.php';

$db = $client->selectDatabase('productdb');
$ratingCollection = $db->selectCollection('ratings');

if (isset($_POST['rating']) && isset($_POST['productId']) && isset($_POST['userId'])) {
    $rating = intval($_POST['rating']);
    $productId = intval($_POST['productId']);
    $userId = intval($_POST['userId']);

    $existingRating = $ratingCollection->findOne([
        'product_id' => (int)$productId,
        'user_id' => (int)$userId
    ]);

    if ($existingRating) {
        // UPDATE existing rating
        $ratingCollection->updateOne(
            ['_id' => $existingRating['_id']],
            ['$set' => ['value' => (int)$rating]]
        );
    } else {
        // ADD new rating
        $ratingCollection->insertOne([
            'product_id' => (int)$productId,
            'user_id' => (int)$userId,
            'value' => (int)$rating
        ]);
    }
}
?>