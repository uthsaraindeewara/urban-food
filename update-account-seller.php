<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check session
if (!isset($_SESSION['user']['userID'])) {
    echo "User session not set.";
    exit();
}

$cusID = $_SESSION['user']['userID'];

// Oracle connection
$conn = oci_connect('system', 'oracle_password', '//localhost:1521/XEPDB1');
if (!$conn) {
    $e = oci_error();
    die("Connection failed: " . $e['message']);
}

// Sanitize POST data
$username = trim($_POST['username']);
$contactNo = trim($_POST['contactNo']);
$email = trim($_POST['email']);
$Address = trim($_POST['Address']);

// Email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email format.";
    exit();
}

// Prepare procedure call
$sql = "BEGIN 
            update_account_seller(:user_id, :username, :email, :contact, :farm_address); 
        END;";

$stmt = oci_parse($conn, $sql);

// Bind parameters
oci_bind_by_name($stmt, ":user_id", $cusID);
oci_bind_by_name($stmt, ":username", $username);
oci_bind_by_name($stmt, ":email", $email);
oci_bind_by_name($stmt, ":contact", $contactNo);
oci_bind_by_name($stmt, ":farm_address", $Address);

// Execute procedure
if (!oci_execute($stmt)) {
    $e = oci_error($stmt);
    echo "Error updating details: " . $e['message'];
    exit();
}

oci_free_statement($stmt);
oci_close($conn);

// Redirect after update
header("Location: edit-account-seller.php");
exit();
?>
