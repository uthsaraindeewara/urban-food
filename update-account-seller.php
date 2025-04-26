<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);


if (!isset($_SESSION['user']['userID'])) {
    echo "User session not set.";
    exit();
}

$cusID = $_SESSION['user']['userID'];
// Database connection
$conn = oci_connect("system", "sys112233", "//localhost/XEPDB1");
if (!$conn) {
    die("Database connection failed: " . oci_error()['message']);
}


$username = trim($_POST['username']);
$name= trim($_POST['name']);    
$contactNo = trim($_POST['contactNo']);
$email = trim($_POST['email']);
$Address = trim($_POST['Address']);

// Email validation 
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email format.";
    exit();
}

// procedure call
$sql = "BEGIN 
            update_account_seller(:user_id, :username,:name, :email, :contact, :farm_address); 
        END;";

$stmt = oci_parse($conn, $sql);

// Bind parameters
oci_bind_by_name($stmt, ":user_id", $cusID);
oci_bind_by_name($stmt, ":username", $username);
oci_bind_by_name($stmt, ":name", $name);
oci_bind_by_name($stmt, ":email", $email);
oci_bind_by_name($stmt, ":contact", $contactNo);
oci_bind_by_name($stmt, ":farm_address", $Address);

// Execute procedure
if (!oci_execute($stmt)) {
    echo "Something went wrong. Please try again later";
    exit();
}

oci_free_statement($stmt);
oci_close($conn);


header("Location: edit-account-seller.php");
exit();
?>