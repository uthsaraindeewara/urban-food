<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Prepare PL/SQL call
    $stmt = oci_parse($conn, "
        BEGIN
            signup_customer(
                :username,
                :email,
                :password,
                :user_id,
                :status
            );
        END;
    ");

    // Bind inputs
    oci_bind_by_name($stmt, ":username", $username);
    oci_bind_by_name($stmt, ":email", $email);
    oci_bind_by_name($stmt, ":password", $password);

    // Bind outputs
    oci_bind_by_name($stmt, ":user_id", $userID, 32);
    oci_bind_by_name($stmt, ":status", $status, 20);

    $exec = oci_execute($stmt);

    if (!$exec) {
        $e = oci_error($stmt);
        echo "Database execution error: " . htmlentities($e['message']);
        exit();
    }

    if ($exec && $status == 'SUCCESS') {
        $_SESSION['user'] = [
            'userID' => $userID,
            'name' => $username,
            'userType' => 'customer'
        ];

        header("Location: login.html");
        exit();
    } else {
        $e = oci_error($stmt);
    $errorMessage = isset($e['message']) ? addslashes($e['message']) : 'Unknown error';
    echo "<script>alert('Error registering customer: $errorMessage');</script>";
    echo "Status: $status, UserID: $userID";
    }

    oci_free_statement($stmt);
    oci_close($conn);
}
?>