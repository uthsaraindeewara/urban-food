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


if (isset($_GET['delete_id'])) {
    $deleteUserID = $_GET['delete_id'];
    $stmt = oci_parse($conn, "BEGIN admin_delete_user(:admin_id, :id); END;");
    oci_bind_by_name($stmt, ':id', $deleteUserID);
    oci_bind_by_name($stmt, ':admin_id', $cusID);
    if (oci_execute($stmt)) {
        echo "<script>alert('User deleted successfully.'); window.location.href = 'admin-dashboard.php';</script>";
    } else {
        $e = oci_error($stmt); 
        echo "<script>alert('Failed to delete user: " . $e['message'] . "');</script>";
        }
    oci_free_statement($stmt);
}

// Search
$searchTerm = "";
$isSearch = false;
if (isset($_POST['search'])) {
    $searchTerm = $_POST['searchTerm'];
    $isSearch = true;
}

// Get customers
$stmtCustomer = oci_parse($conn, $isSearch ? "BEGIN admin_serach_customer(:term, :cursor); END;" : "BEGIN admin_get_customer(:cursor); END;");
$cursorCustomer = oci_new_cursor($conn);

if ($isSearch) {
    oci_bind_by_name($stmtCustomer, ':term', $searchTerm);
}
oci_bind_by_name($stmtCustomer, ':cursor', $cursorCustomer, -1, OCI_B_CURSOR);
oci_execute($stmtCustomer);
oci_execute($cursorCustomer);

// Get sellers
$stmtSeller = oci_parse($conn, $isSearch ? "BEGIN admin_serach_seller(:term, :cursor); END;" : "BEGIN admin_get_seller(:cursor); END;");
$cursorSeller = oci_new_cursor($conn);

if ($isSearch) {
    oci_bind_by_name($stmtSeller, ':term', $searchTerm);
}
oci_bind_by_name($stmtSeller, ':cursor', $cursorSeller, -1, OCI_B_CURSOR);
oci_execute($stmtSeller);
oci_execute($cursorSeller);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin-dashboard.css">
</head>
<body>

    <div class="container">
        <h2>Admin Dashboard</h2>

        <!-- Search Form -->
        <form action="admin-dashboard.php" method="POST">
            <input type="text" name="searchTerm" placeholder="Search by username or email" required>
            <button type="submit" name="search">Search</button>
        </form>

        <h3>Customers</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Shipping Address</th>
                    <th>Billing Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while (($row = oci_fetch_array($cursorCustomer, OCI_ASSOC + OCI_RETURN_NULLS)) != false): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['USERNAME']); ?></td>
        <td><?php echo htmlspecialchars($row['EMAIL']); ?></td>
        <td><?php echo htmlspecialchars($row['SHIPPING_ADDRESS']); ?></td>
        <td><?php echo htmlspecialchars($row['BILLING_ADDRESS']); ?></td>
        <td>
            <a href="admin-dashboard.php?delete_id=<?php echo $row['USER_ID']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
        </td>
    </tr>
<?php endwhile; ?>

            </tbody>
        </table>

        <h3>Sellers</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Farm Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while (($row = oci_fetch_array($cursorSeller, OCI_ASSOC + OCI_RETURN_NULLS)) != false): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['USERNAME']); ?></td>
        <td><?php echo htmlspecialchars($row['EMAIL']); ?></td>
        <td><?php echo htmlspecialchars($row['FARM_ADDRESS']); ?></td>
        <td>
            <a href="admin-dashboard.php?delete_id=<?php echo $row['USER_ID']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
        </td>
    </tr>
<?php endwhile; ?>

            </tbody>
        </table>

    </div>

</body>
</html>

<?php
oci_free_statement($stmtCustomer);
oci_free_statement($stmtSeller);
oci_free_statement($cursorCustomer);
oci_free_statement($cursorSeller);
oci_close($conn);


?>