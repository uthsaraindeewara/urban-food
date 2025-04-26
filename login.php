<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = oci_parse($conn, "
        BEGIN
            user_login(
                :email,
                :password,
                :user_id,
                :full_name,
                :user_type,
                :status
            );
        END;
    ");

    // Bind input parameters
    oci_bind_by_name($stmt, ":email", $email);
    oci_bind_by_name($stmt, ":password", $password);

    // Bind output parameters
    oci_bind_by_name($stmt, ":user_id", $userID, 32);
    oci_bind_by_name($stmt, ":full_name", $fullName, 100);
    oci_bind_by_name($stmt, ":user_type", $userType, 20);
    oci_bind_by_name($stmt, ":status", $status, 20);

    $exec = oci_execute($stmt);

    if ($exec && $status == 'SUCCESS') {
        $_SESSION['user'] = [
            'userID' => $userID,
            'name' => $fullName,
            'userType' => $userType
        ];

        if ($userType == 'admin') {
            header("Location: admin-dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else if ($status == 'NOT_FOUND') {
        echo "<script>alert('Invalid username/email or password.');</script>";
    } else {
        echo "<script>alert('An error occurred. Please try again later.');</script>";
    }

    oci_free_statement($stmt);
    oci_close($conn);
}
?>
