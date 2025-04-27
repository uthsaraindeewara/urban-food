<?php
session_start();

include "connection.php";

$email = trim($_POST['email'] ?? '');
$newPassword = $_POST['newPassword'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';

// Validation
if (empty($email) || empty($newPassword) || empty($confirmPassword)) {
    echo "<script>alert('Please fill all fields.');</script>";
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid email format.');</script>";
    exit();
}

if ($newPassword !== $confirmPassword) {
    echo "<script>alert('Passwords do not match.');</script>";
    exit();
}

// Call procedure
$sql = "BEGIN reset_user_password(:email, :newPassword); END;";
$stmt = oci_parse($conn, $sql);


oci_bind_by_name($stmt, ":email", $email);
oci_bind_by_name($stmt, ":newPassword", $newPassword);

// Execute procedure
if (oci_execute($stmt)) {
    echo "<script>alert('Password reset successfully.'); window.location.href='login.html';</script>";
} else {
    $e = oci_error($stmt);
    if (strpos($e['message'], 'ORA-20001') !== false) {
        echo "<script>alert('No user found with that email.');</script>";
    } else {
        echo "Failed to reset password: " . $e['message'];
    }
}

oci_free_statement($stmt);
oci_close($conn);
?>