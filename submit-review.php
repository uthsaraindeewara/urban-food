<?php
require 'connection1.php';

use MongoDB\BSON\UTCDateTime;

$db = $client->selectDatabase('productdb');
$ratingCollection = $db->selectCollection('reviews');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['description']) && isset($data['productId']) && isset($data['userId']) && isset($data['date'])) {
    $productId = intval($data['productId']);
    $userId = intval($data['userId']);
    $description = $data['description'];
    $date = new UTCDateTime(strtotime($data['date']) * 1000);

    // ADD new rating
    $ratingCollection->insertOne([
        'product_id' => (int)$productId,
        'user_id' => (int)$userId,
        'description' => $description,
        'date' => $date
    ]);

    echo json_encode(['success' => true, 'message' => 'Review submitted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
}
?>