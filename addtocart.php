<?php
session_start();

// Connect to the database
$servername = "localhost:3306";
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
$stmt = $conn->prepare("INSERT INTO product_cart (productID, cartID, size, quantity)
                        VALUES (?, (SELECT cartID FROM cart WHERE cusID = ?), ?, ?)");
$stmt->bind_param("iisi", $productID, $cusID, $size, $quantity);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    header("Location: cart.php");
    exit();
} else {
    echo "Failed to add product to cart.";
}

$stmt->close();
$conn->close();
?>