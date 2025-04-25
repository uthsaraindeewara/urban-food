<?php
// Oracle DB connection
$conn = oci_connect('your_oracle_username', 'your_oracle_password', 'localhost/XE');

if (!$conn) {
    $e = oci_error();
    die("Connection failed: " . $e['message']);
}

// Handle user deletion
if (isset($_GET['delete_id'])) {
    $deleteUserID = $_GET['delete_id'];
    $stmt = oci_parse($conn, "BEGIN admin_delete_user(:id); END;");
    oci_bind_by_name($stmt, ':id', $deleteUserID);
    if (oci_execute($stmt)) {
        echo "<script>alert('User deleted successfully.'); window.location.href = 'admin-dashboard.php';</script>";
    } else {
        echo "<script>alert('Failed to delete user.');</script>";
    }
    oci_free_statement($stmt);
}

// Search handling
$searchTerm = "";
$isSearch = false;
if (isset($_POST['search'])) {
    $searchTerm = $_POST['searchTerm'];
    $isSearch = true;
}

// Fetch customers
$stmtCustomer = oci_parse($conn, $isSearch ? "BEGIN admin_serach_customer(:term, :cursor); END;" : "BEGIN admin_get_customer(:cursor); END;");
$cursorCustomer = oci_new_cursor($conn);

if ($isSearch) {
    oci_bind_by_name($stmtCustomer, ':term', $searchTerm);
}
oci_bind_by_name($stmtCustomer, ':cursor', $cursorCustomer, -1, OCI_B_CURSOR);
oci_execute($stmtCustomer);
oci_execute($cursorCustomer);

// Fetch sellers
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
                <?php while ($row = $resultCustomer->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['shipping_address']); ?></td>
                        <td><?php echo htmlspecialchars($row['billing_address']); ?></td>
                        <td>
                            <a href="admin-dashboard.php?delete_id=<?php echo $row['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
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
                <?php while ($row = $resultSeller->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['farm_address']); ?></td>
                        <td>
                            <a href="admin-dashboard.php?delete_id=<?php echo $row['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>

</body>
</html>

<?php
$stmtCustomer->close();
$stmtSeller->close();
$conn->close();
?>
