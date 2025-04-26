<?php
session_start();
$conn = oci_connect("system", "sys112233", "//localhost/XEPDB1");
if (!$conn) {
    die("Database connection failed: " . oci_error()['message']);
}

if (isset($_POST['updateStatus'])) {
    $orderItemID = $_POST['updateStatus'];

   
    $stmt = oci_parse($conn, "BEGIN UpdateOrderItemStatus(:orderItemID, 'SHIPPED'); END;");
    oci_bind_by_name($stmt, ":orderItemID", $orderItemID);
    oci_execute($stmt);
     
    oci_commit($conn);

    header("Location: seller_orders.php");
    exit;
}
?>