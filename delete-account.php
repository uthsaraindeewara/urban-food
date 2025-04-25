<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user']['userID'])) {
    echo "<script>alert('User not logged in.'); window.location.href = 'login.html';</script>";
    exit();
}

$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "storedb1";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userID = $_SESSION['user']['userID'];
$userType = $_SESSION['user']['userType'];

// Delete from customer/seller table
if ($userType === 'customer') {
    $deleteQuery1 = "DELETE FROM customer WHERE customer_id = ?";
} elseif ($userType === 'seller') {
    $deleteQuery1 = "DELETE FROM seller WHERE seller_id = ?";
} else {
    echo "<script>alert('Invalid user type.'); window.location.href='login.html';</script>";
    exit();
}

$stmt1 = $conn->prepare($deleteQuery1);
$stmt1->bind_param("i", $userID);
$stmt1->execute();
$stmt1->close();

// Delete from user_table
$deleteQuery2 = "DELETE FROM user_table WHERE user_id = ?";
$stmt2 = $conn->prepare($deleteQuery2);
$stmt2->bind_param("i", $userID);

if ($stmt2->execute()) {
    session_destroy(); // Logout user
    echo "<script>alert('Account deleted successfully.'); window.location.href='index.php';</script>";
} else {
    echo "<script>alert('Failed to delete account.'); window.history.back();</script>";
}

$stmt2->close();
$conn->close();
?>
