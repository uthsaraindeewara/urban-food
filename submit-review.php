<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "storedb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Get the JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate the received data
if (!isset($data['date'], $data['time'], $data['description'], $data['productID'])) {
    echo json_encode(["error" => "Invalid input"]);
    exit;
}

$date = $conn->real_escape_string($data['date']);
$time = $conn->real_escape_string($data['time']);
$description = $conn->real_escape_string($data['description']);
$productID = $conn->real_escape_string($data['productID']);
$cusID = $conn->real_escape_string($data['cusID']);

// Insert review into the database
$sql = "INSERT INTO review (date, time, description, cusID, productID) VALUES ('$date', '$time', '$description', '$cusID', '$productID')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["message" => "Review submitted successfully!", "id" => $conn->insert_id]);
} else {
    echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
}

// Close connection
$conn->close();
?>