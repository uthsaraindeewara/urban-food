<?php
// Database connection
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "storedb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the rating and product ID are received
if (isset($_POST['rating']) && isset($_POST['productId'])) {
    $rating = intval($_POST['rating']);
    $productId = intval($_POST['productId']);

    // Insert the rating into the database (assuming a 'ratings' table)
    $sql = "INSERT INTO rating (value, productID) VALUES (?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $rating, $productId);

    if ($stmt->execute()) {
        echo "Rating submitted successfully";
    } else {
        echo "Error submitting rating: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Invalid request";
}

$conn->close();
?>