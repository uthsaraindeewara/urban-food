<?php
require 'vendor/autoload.php'; // If you're using Composer

$uri = "mongodb+srv://ui123:ui123@cluster0.yympynv.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0";

try {
    $client = new MongoDB\Client($uri);
    $collection = $client->selectCollection('<dbname>', '<collectionname>');
    
    // Example: find all documents
    $cursor = $collection->find();
    foreach ($cursor as $document) {
        
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>