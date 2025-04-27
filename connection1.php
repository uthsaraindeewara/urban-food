<?php
require 'vendor/autoload.php'; // If you're using Composer

$uri = "mongodb://localhost:27017";
$dbName = "productdb";

try {
    $client = new MongoDB\Client($uri);
    
    return [
        'client' => $client,
        'db' => $client->selectDatabase($dbName)
    ];
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>