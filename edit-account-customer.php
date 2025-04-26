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

// Call procedure
$sql = "BEGIN 
            edit_account_customer(:user_id, :username, :email, :contact, :shipping, :billing); 
        END;";

$stmt = oci_parse($conn, $sql);

oci_bind_by_name($stmt, ":user_id", $cusID);


oci_bind_by_name($stmt, ":username", $username, 100);
oci_bind_by_name($stmt, ":email", $email, 100);
oci_bind_by_name($stmt, ":contact", $contactNo, 20);
oci_bind_by_name($stmt, ":shipping", $shippingAddress, 200);
oci_bind_by_name($stmt, ":billing", $billingAddress, 200);

if (!oci_execute($stmt)) {
    $e = oci_error($stmt);
    echo "Error fetching user data: " . $e['message'];
    exit();
}

oci_free_statement($stmt);
oci_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Account Details</title>
    <link rel="stylesheet" href="edit-account.css">
</head>
<body>

    <div class="container">
        <h2>Edit Account Details</h2>
        <form action="update-account-customer.php" method="POST">

            <!-- Username -->
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
            </div>

            <!-- Address -->
            <div class="form-group">
                <label for="address">Shipping Address</label>
                <input type="text" id="address" name="shippingAddress" value="<?php echo htmlspecialchars($shippingAddress ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="address">Billing Address</label>
                <input type="text" id="address" name="billingAddress" value="<?php echo htmlspecialchars($billingAddress ?? ''); ?>"required>
            </div>


            <!-- Contact Number -->
            <div class="form-group">
                <label for="contactNo">Contact Number</label>
                <input type="tel" id="contactNo" name="contactNo" value="<?php echo htmlspecialchars($contactNo ?? ''); ?>" required>
            </div>
            
            
          <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            

            <div class="form-group">
                <button type="submit">Save Changes</button>
            </div>
        </form>
    </div>

    

</body>
</html>