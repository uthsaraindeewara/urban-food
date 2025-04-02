<?php
session_start();
require 'vendor/autoload.php'; // Ensure you have your autoload file

// Database connection setup (replace with your actual database connection code)
$conn = new mysqli('localhost:3306', 'root', '', 'storedb'); // Update with your credentials
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cusName = $_POST['username']; // Use username as customer name
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Insert customer data into the customer table
    $stmt = $conn->prepare("INSERT INTO customer (cusName, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $cusName, $email);

    if ($stmt->execute()) {
        $cusID = $stmt->insert_id; // Get the newly created customer ID

        // Insert user account data into the cus_account table
        $accountStmt = $conn->prepare("INSERT INTO cus_account (username, password, cusID) VALUES (?, ?, ?)");
        $accountStmt->bind_param("ssi", $cusName, $password, $cusID);

        if ($accountStmt->execute()) {
            $cartStmt = $conn->prepare("INSERT INTO cart (cusID) VALUES (?)");
            $cartStmt->bind_param("s", $cusID);

            $cartStmt->execute();

            $wishlistStmt = $conn->prepare("INSERT INTO wishlist (cus_id) VALUES (?)");
            $wishlistStmt->bind_param("s", $cusID);

            $wishlistStmt->execute();

            $_SESSION['user'] = [
                'user_id' => $accountStmt->insert_id, // Get the newly created account ID
                'name' => $cusName, // Store username in session
                'cusID' => $cusID
            ];
            header("Location: index.php"); // Redirect to index.php or any other page
            exit();
        } else {
            echo "Error creating account: " . $accountStmt->error; // Handle error
        }
    } else {
        echo "Error adding customer: " . $stmt->error; // Handle error
    }
}

$stmt->close();
$accountStmt->close();
$conn->close();
?>