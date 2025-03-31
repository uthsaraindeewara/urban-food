<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli("localhost:3307", "root", "", "storedb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password']; // The password entered by the user

    // Check if user exists by username or email
    $stmt = $conn->prepare("SELECT cus_account.*, customer.email FROM cus_account 
                            INNER JOIN customer ON cus_account.cusID = customer.cusID 
                            WHERE cus_account.username = ? OR customer.email = ?");
    if ($stmt === false) {
        die("Error in query: " . $conn->error);
    }

    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify password (entered password against the hash in the database)
        if (password_verify($password, $row['password'])) {
            // Start session
            $_SESSION['user'] = [
                'name' => $row['username'],
                'cusID' => $row['cusID'] 
            ];
            header("Location: index.php");
            exit();
        } else {
            // Incorrect password
            echo "<script>alert('Password verification failed. Invalid username/email or password.');<script>";
        }
    } else {
        // No user found
        echo "<script>alert('No user found with this username/email.);<script>";
    }

    $stmt->close();
}

$conn->close();
?>