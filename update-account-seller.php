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
$sellerName = trim($_POST['sellerName']);
$contactNo = trim($_POST['contactNo']);
$email = trim($_POST['email']);
$Address = trim($_POST['Address']);

// Email validation 
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email format.";
    exit();
}

if (isset($_FILES['sellerImage']) && $_FILES['sellerImage']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['sellerImage']['tmp_name'];
    $fileName = $_FILES['sellerImage']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if ($fileExtension !== 'jpg') {
        echo "<script>alert('Only JPG images are allowed.'); window.history.back();</script>";
        exit();
    }

    $destinationDirectory = 'sellerImages/';
    if (!is_dir($destinationDirectory)) {
        mkdir($destinationDirectory, 0755, true); // Create folder if it doesn't exist
    }

    $newFileName = "seller-" . $cusID . ".jpg";
    $destinationPath = $destinationDirectory . $newFileName;

    if (!move_uploaded_file($fileTmpPath, $destinationPath)) {
        echo "<script>alert('Failed to upload image.'); window.history.back();</script>";
        exit();
    }
}

// procedure call
$sql = "BEGIN 
            update_account_seller(:user_id, :username, :email, :seller_name, :contact, :farm_address); 
        END;";

$stmt = oci_parse($conn, $sql);

// Bind parameters
oci_bind_by_name($stmt, ":user_id", $cusID);
oci_bind_by_name($stmt, ":username", $username);
oci_bind_by_name($stmt, ":name", $name);
oci_bind_by_name($stmt, ":email", $email);
oci_bind_by_name($stmt, ":seller_name", $sellerName);
oci_bind_by_name($stmt, ":contact", $contactNo);
oci_bind_by_name($stmt, ":farm_address", $Address);

if (!oci_execute($stmt)) {
    $e = oci_error($stmt);
    echo "Database error: " . htmlentities($e['message']);
    exit();
}

oci_free_statement($stmt);
oci_close($conn);


header("Location: index.php");
exit();
?>