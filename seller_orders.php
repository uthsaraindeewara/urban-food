<?php
session_start();

include "connection.php";


$sellerID = 1; 
$stmt = oci_parse($conn, "BEGIN GetSellersOrderItems(:sellerID, :result); END;");
oci_bind_by_name($stmt, ":sellerID", $sellerID);
$cursor = oci_new_cursor($conn);
oci_bind_by_name($stmt, ":result", $cursor, -1, OCI_B_CURSOR);
oci_execute($stmt);
oci_execute($cursor);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Seller Orders</title>
    <link rel="stylesheet" href="seller_orders.css">
</head>
<body>
    <h1>Seller Orders</h1>
    <form method="POST" action="update_status.php">
        <table>
            <thead>
                <tr>
                    <th>Order Item ID</th>
                    <th>Order ID</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = oci_fetch_assoc($cursor)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['ITEM_ID']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ORDER_ID']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['PRODUCT_NAME']) . "</td>";
                    echo "<td>Rs. " . htmlspecialchars($row['ITEM_PRICE']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ITEM_QUANTITY']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ORDER_DATE']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ITEM_STATUS']) . "</td>";
                    echo "<td>
                        <button type='submit' name='updateStatus' value='" . htmlspecialchars($row['ITEM_ID']) . "'>Mark as Shipped</button>
                    </td>";
                    echo "</tr>";
                }

                oci_free_statement($stmt);
                oci_free_cursor($cursor);
                ?>
            </tbody>
        </table>
    </form>
</body>
</html>