<?php
session_start();

// Connect to the database
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "storedb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from POST request
$productID = isset($_POST['productId']) ? intval($_POST['productId']) : 0;
$cusID = $_SESSION['user']['cusID'];
$size = isset($_POST['size']) ? $_POST['size'] : '';
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

// Insert into product_cart
$stmt = $conn->prepare("INSERT INTO product_wishlist (wishlist_id, product_id, size, quantity)
                        VALUES ((SELECT wishlist_id FROM wishlist WHERE cus_id = ?), ?, ?, ?)");
$stmt->bind_param("iisi", $cusID, $productID, $size, $quantity);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Product added to wishlist successfully!";
} else {
    echo "Failed to add product to wishlist.";
}

$stmt->close();
$conn->close();
?>