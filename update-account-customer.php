<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user']['userID'])) {
    echo "User session not set.";
    exit();
}

$cusID = $_SESSION['user']['userID'];

include "connection.php";


$username = trim($_POST['username']);
$shippingAddress = trim($_POST['shippingAddress']);
$billingAddress = trim($_POST['billingAddress']);
$contactNo = trim($_POST['contactNo']);
$email = trim($_POST['email']);

// Email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email format.";
    exit();
}

// procedure call
$sql = "BEGIN 
            update_account_customer(:user_id, :username, :email, :contact, :shipping, :billing); 
        END;";

$stmt = oci_parse($conn, $sql);

oci_bind_by_name($stmt, ":user_id", $cusID);
oci_bind_by_name($stmt, ":username", $username);
oci_bind_by_name($stmt, ":email", $email);
oci_bind_by_name($stmt, ":contact", $contactNo);
oci_bind_by_name($stmt, ":shipping", $shippingAddress);
oci_bind_by_name($stmt, ":billing", $billingAddress);

// Execute procedure
if (!oci_execute($stmt)) {
    $e = oci_error($stmt);
    echo "Error updating details: " . $e['message'];
    exit();
}

oci_free_statement($stmt);
oci_close($conn);

header("Location: index.php");
exit();
?>