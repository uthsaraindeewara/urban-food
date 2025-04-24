<?php
// Start session and connect to database
session_start();


if (!isset($_SESSION['user']['userID'])) {
    echo "User session not set.";
    exit();
}
$cusID = $_SESSION['user']['userID'];
$userType = $_SESSION['user']['userType'];

if($userType=='customer'){
    header("Location: edit-account-customer.php");
    exit();
}else{
    header("Location: edit-account-seller.php");
    exit();
}
?>